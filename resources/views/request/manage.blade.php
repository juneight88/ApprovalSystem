<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage Requests</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel ="stylesheet" href="{{asset('css/manageRequest.css')}}">
   <style>
        .table-responsive {
            overflow-x: auto;
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-weight: 500;
        }
        .comment-section {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            margin-top: 5px;
        }
    </style>
</head>
<body class="container-fluid mt-5">
    <h2 class="text-center mb-4">Manage Requests</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Request ID</th>
                    <th>Requester</th>
                    <th>Department</th>
                    <th>Test Category</th>
                    <th>Date Request</th>
                    <th>Subject</th>
                    <th>Modality</th>
                    <th>Exam Date</th>
                    <th>Time Allotment</th>
                    <th>Paper Size</th>
                    <th>Printing Mode</th>
                    <th>Pages</th>
                    <th>Copies</th>
                    <th>Status</th>
                    <th>File</th>
                    <th>Actions</th>
                    <th>Comments</th>
                    <th>Approval Sheet</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($requests as $request)
                    <tr>
                        <td>{{ $request->id }}</td>
                        <td>{{ optional($request->user)->username }}</td>
                        <td>{{ $request->department }}</td>
                        <td>{{ $request->test_category }}</td>
                        <td>{{ date('M d, Y', strtotime($request->date_request)) }}</td>
                        <td>
                            @if($request->subject_id)
                                {{ optional($request->subject)->code }} - {{ optional($request->subject)->name }}
                            @else
                                {{ $request->specific_subject }}
                            @endif
                        </td>
                        <td>{{ $request->modality_of_learning }}</td>
                        <td>{{ date('M d, Y', strtotime($request->date_exam)) }}</td>
                        <td>{{ $request->exam_time_allotment }} minutes</td>
                        <td>{{ $request->paper_size }}</td>
                        <td>{{ $request->printing_mode }}</td>
                        <td>{{ $request->number_of_pages }}</td>
                        <td>{{ $request->number_of_copies }}</td>
                        <td>
                            @switch($request->status)
                                @case('pending')
                                    <span class="status-badge bg-warning text-dark">Awaiting Subject Coordinator Approval</span>
                                    @break
                                @case('coordinator_approved')
                                    <span class="status-badge bg-info text-white">Awaiting Head of Office Approval</span>
                                    @break
                                @case('final_approved')
                                    <span class="status-badge bg-success">Approved by Head of Office</span>
                                    @break
                                @case('rejected')
                                    <span class="status-badge bg-danger">
                                        Rejected
                                        @if($request->rejected_by)
                                            by {{ optional(App\Models\User::find($request->rejected_by))->username }}
                                        @endif
                                    </span>
                                    @break
                                @default
                                    <span class="status-badge bg-secondary">{{ $request->status }}</span>
                            @endswitch
                        </td>
                        <td>
                            @if ($request->file_path)
                                <a href="{{ asset('storage/' . $request->file_path) }}" target="_blank" class="btn btn-info btn-sm">
                                    View File
                                </a>
                            @else
                                No file
                            @endif
                        </td>
                        <td>
                            @php
                                $canApproveAsCoordinator = false;
                                $canApproveAsHead = false;
                                
                                if ($user->role === 'Subject Coordinator') {
                                    if ($user->department === 'BASIC EDUCATION') {
                                        $subjectHandled = json_decode($user->subject_handled) ?? [$user->subject_handled];
                                        if ($request->subject && in_array($request->subject->name, (array)$subjectHandled)) {
                                            $canApproveAsCoordinator = true;
                                        }
                                    } else {
                                        $canApproveAsCoordinator = $request->coordinator_id === $user->id;
                                    }
                                }

                                if (in_array($user->role, ['Head of Office', 'Elementary Head of Office', 'Junior High School Head of Office', 'Senior High School Head of Office'])) {
                                    if ($user->department === 'BASIC EDUCATION') {
                                        $levelMap = [
                                            'Elementary Head of Office' => 'ELEM',
                                            'Junior High School Head of Office' => 'JHS',
                                            'Senior High School Head of Office' => 'SHS',
                                            'Head of Office' => strtoupper($user->program)
                                        ];
                                        
                                        $userLevel = $levelMap[$user->role];
                                        
                                        if ($request->subject && strtoupper($request->subject->education_level) === $userLevel) {
                                            $canApproveAsHead = true;
                                        }
                                    } else {
                                        $canApproveAsHead = $request->head_office_id === $user->id;
                                    }
                                }
                            @endphp

                            @if($request->status === 'pending' && $canApproveAsCoordinator)
                                <form action="{{ route('approve.coordinator', $request->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm">
                                        <i class="fas fa-check"></i> Approve as Coordinator
                                    </button>
                                </form>
                            @endif

                            @if($request->status === 'coordinator_approved' && $canApproveAsHead)
                                <form action="{{ route('approve.dean', $request->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm">
                                        <i class="fas fa-check"></i> Final Approval
                                    </button>
                                </form>
                            @endif

                            @if(($request->status === 'pending' && $canApproveAsCoordinator) || 
                                ($request->status === 'coordinator_approved' && $canApproveAsHead))
                                <form action="{{ route('reject.request', $request->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="fas fa-times"></i> Reject
                                    </button>
                                </form>
                            @endif
                        </td>
                        <td>
                            <button class="btn btn-info btn-sm" onclick="toggleComment({{ $request->id }})">
                                <i class="fas fa-comments"></i> View Comments
                            </button>
                            <div id="comment-{{ $request->id }}" class="comment-section" style="display: none;">
                                <form action="{{ route('add.comment', $request->id) }}" method="POST">
                                    @csrf
                                    <div class="input-group mb-3">
                                        <input type="text" name="comment" class="form-control" placeholder="Add a comment">
                                        <button class="btn btn-primary" type="submit">
                                            <i class="fas fa-paper-plane"></i>
                                        </button>
                                    </div>
                                </form>
                                <div class="comments-list">
                                    @if($request->comments)
                                        <p>{{ $request->comments }}</p>
                                    @else
                                        <p class="text-muted">No comments yet</p>
                                    @endif
                                </div>
                            </div>
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
                                    <i class="fas fa-clock"></i> Sheet Pending
                                </span>
                            @else
                                <span class="badge bg-secondary">
                                    <i class="fas fa-hourglass"></i> Not Available
                                </span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <a href="/dashboard" class="btn btn-primary mt-3">Back to Dashboard</a>

    <script>
        function toggleComment(requestId) {
            const commentSection = document.getElementById(`comment-${requestId}`);
            commentSection.style.display = commentSection.style.display === 'none' ? 'block' : 'none';
        }
    </script>
</body>
</html>
