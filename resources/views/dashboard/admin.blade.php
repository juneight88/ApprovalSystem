    @extends('layouts.app')

    @section('title', 'Admin Dashboard')
    @section('nav_title', 'Admin Dashboard')

    @section('content')
        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card dashboard-card">
                    <div class="card-body text-center">
                        <i class="fas fa-check-circle card-icon text-success"></i>
                        <div class="stats-number">{{ $approvedCount ?? 0 }}</div>
                        <div class="stats-label">Approved Requests</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card dashboard-card">
                    <div class="card-body text-center">
                        <i class="fas fa-user-cog card-icon text-primary"></i>
                        <div class="stats-number">{{ $adminCount ?? 0 }}</div>
                        <div class="stats-label">Admin Assigned</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card dashboard-card">
                    <div class="card-body text-center">
                        <i class="fas fa-user-graduate card-icon text-info"></i>
                        <div class="stats-number">{{ $assistantCount ?? 0 }}</div>
                        <div class="stats-label">Student Assistant Assigned</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Approved Requests Table -->
        <div class="card">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Approved Requests</h5>
                <div>
                    <button class="btn btn-light btn-sm" id="exportBtn">
                        <i class="fas fa-file-excel"></i> Export to Excel
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="approvedRequestsTable">
                        <thead class="table-light">
                            <tr>
                                <th>Request ID</th>
                                <th>Requester</th>
                                <th>Subject</th>
                                <th>Test Category</th>
                                <th>Exam Date</th>
                                <th>Status</th>
                                <th>Producer</th>
                                <th>Claimants</th>
                                <th>Approval Sheet</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($approvedRequests ?? [] as $request)
                            <tr>
                                <td>{{ $request->id }}</td>
                                <td>{{ optional($request->user)->first_name }} {{ optional($request->user)->last_name }}</td>
                                <td>
                                    @if($request->subject_id)
                                        {{ optional($request->subject)->name }}
                                    @else
                                        {{ $request->specific_subject }}
                                    @endif
                                </td>
                                <td>{{ $request->test_category }}</td>
                                <td>{{ $request->date_exam ? date('M d, Y', strtotime($request->date_exam)) : ($request->date_required ? date('M d, Y', strtotime($request->date_required)) : 'N/A') }}</td>
                                <td>
                                    <span class="badge bg-success">{{ ucwords(str_replace('_', ' ', $request->status)) }}</span>
                                </td>
                                <td>
                                    <form action="{{ route('assign.producer', $request->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <select name="producer" class="form-select form-select-sm @if($request->producer) border-success @endif" 
                                                onchange="this.form.submit()" style="width: 150px;">
                                            <option value="">Assign Producer</option>
                                            <option value="admin" {{ $request->producer == 'admin' ? 'selected' : '' }}>Admin</option>
                                            <option value="student_assistant" {{ $request->producer == 'student_assistant' ? 'selected' : '' }}>Student Assistant</option>
                                        </select>
                                    </form>
                                </td>
                                <td>
                                    <form action="{{ route('update.claimants', $request->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="text" 
                                               name="claimants" 
                                               class="form-control form-control-sm @if($request->claimants) border-success @endif"
                                               value="{{ $request->claimants }}"
                                               placeholder="Enter claimants"
                                               onchange="this.form.submit()"
                                               style="width: 150px;">
                                    </form>
                                </td>
                                <td>
                                    @if($request->status === 'final_approved' && $request->generated_document_path)
                                        <a href="{{ route('request.viewApprovalSheet', $request->id) }}" 
                                           class="btn btn-success btn-sm" 
                                           target="_blank">
                                            <i class="fas fa-file-alt"></i> View Approval Sheet
                                        </a>
                                    @elseif($request->status === 'final_approved')
                                        <span class="badge bg-warning text-dark">
                                            <i class="fas fa-clock"></i> Pending Generation
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            <i class="fas fa-hourglass"></i> Awaiting Approval
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <button type="button" 
                                            class="btn btn-info btn-sm" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#requestModal{{ $request->id }}">
                                        <i class="fas fa-eye"></i> Details
                                    </button>
                                </td>
                            </tr>

                            <!-- Request Details Modal -->
                            <div class="modal fade" id="requestModal{{ $request->id }}" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header bg-primary text-white">
                                            <h5 class="modal-title">Request Details #{{ $request->id }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <h6 class="fw-bold">Request Information</h6>
                                                    <hr>
                                                    <p><strong>Name:</strong> {{ optional($request->user)->first_name }} {{ optional($request->user)->last_name }}</p>
                                                    <p><strong>Department:</strong> {{ $request->department }}</p>
                                                    <p><strong>Subject:</strong> 
                                                        @if($request->subject_id)
                                                            {{ optional($request->subject)->name }}
                                                        @else
                                                            {{ $request->specific_subject }}
                                                        @endif
                                                    </p>
                                                    <p><strong>Document Type:</strong> {{ $request->type_of_document ?? $request->test_category }}</p>
                                                    @if($request->test_category)
                                                        <p><strong>Test Category:</strong> {{ $request->test_category }}</p>
                                                    @endif
                                                    @if($request->modality_of_learning)
                                                        <p><strong>Modality:</strong> {{ $request->modality_of_learning }}</p>
                                                    @endif
                                                </div>
                                                <div class="col-md-6">
                                                    <h6 class="fw-bold">Production Details</h6>
                                                    <hr>
                                                    <p><strong>Date Requested:</strong> {{ date('M d, Y', strtotime($request->date_request)) }}</p>
                                                    <p><strong>Required Date:</strong> 
                                                        {{ $request->date_exam ? date('M d, Y', strtotime($request->date_exam)) : 
                                                        ($request->date_required ? date('M d, Y', strtotime($request->date_required)) : 'N/A') }}
                                                    </p>
                                                    @if($request->exam_time_allotment)
                                                        <p><strong>Time Allotment:</strong> {{ $request->exam_time_allotment }} minutes</p>
                                                    @endif
                                                    <p><strong>Number of Pages:</strong> {{ $request->number_of_pages }}</p>
                                                    <p><strong>Number of Copies:</strong> {{ $request->number_of_copies }}</p>
                                                    <p><strong>Status:</strong> 
                                                        <span class="badge bg-success">{{ ucwords(str_replace('_', ' ', $request->status)) }}</span>
                                                    </p>
                                                    <p><strong>Producer:</strong> 
                                                        {{ ucwords(str_replace('_', ' ', $request->producer ?? 'Not Assigned')) }}
                                                    </p>
                                                </div>
                                                @if($request->comments)
                                                    <div class="col-12">
                                                        <h6 class="fw-bold">Comments</h6>
                                                        <hr>
                                                        <p>{{ $request->comments }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            @if($request->file_path)
                                                <a href="{{ url('download/'.$request->id) }}" class="btn btn-primary">
                                                    <i class="fas fa-download"></i> Download Attachment
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @push('scripts')
        <script>
            $(document).ready(function() {
                // Initialize DataTable
                $('#approvedRequestsTable').DataTable({
                    order: [[0, 'desc']],
                    pageLength: 10,
                    responsive: true
                });

                // Handle Export to Excel
                $('#exportBtn').click(function(e) {
                    e.preventDefault();
                    window.location.href = '{{ route("export.approved.requests") }}';
                });

                // Handle Mark as Complete button
                $('.mark-complete').click(function() {
                    const requestId = $(this).data('request-id');
                    if (confirm('Are you sure you want to mark this request as complete?')) {
                        $.post(`/request/${requestId}/complete`, {
                            _token: '{{ csrf_token() }}'
                        })
                        .done(function() {
                            location.reload();
                        })
                        .fail(function() {
                            alert('Failed to mark request as complete. Please try again.');
                        });
                    }
                });
            });
        </script>
        @endpush
    @endsection
