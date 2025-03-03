<!DOCTYPE html>
<html lang="en">
<head>
    <title>Request History</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        .table-responsive {
            overflow-x: auto;
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-weight: 500;
        }
    </style>
</head>
<body class="container-fluid mt-5">
    <h2 class="text-center mb-4">Request History</h2>
    <a href="{{ url('/export-requests') }}" class="btn btn-success mb-3">Export to Excel</a>

    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Request ID</th>
                    <th>Department</th>
                    <th>Test Category</th>
                    <th>Date Request</th>
                    <th>Subject</th>
                    <th>Modality</th>
                    <th>Exam Date</th>
                    <th>Time Allotment</th>
                    <th>Paper Size</th>
                    <th>Printing Mode</th>
                    <th>No. of Pages</th>
                    <th>No. of Copies</th>
                    <th>File</th>
                    <th>Status</th>
                    <th>Comments</th>
                    <th>Approval Sheet</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($requests as $request)
                    <tr>
                        <td>{{ $request->id }}</td>
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
                            @if($request->status === 'rejected')
                                <a href="{{ route('request.edit', $request->id) }}" class="btn btn-warning btn-sm mb-2">
                                    <i class="fas fa-edit"></i> Edit & Resubmit
                                </a>
                            @endif
                            @if($request->file_path)
                                <a href="{{ asset('storage/' . $request->file_path) }}" target="_blank" class="btn btn-info btn-sm">
                                    <i class="fas fa-file-alt"></i> View File
                                </a>
                            @else
                                No file
                            @endif
                        </td>
                        <td>
                            @switch($request->status)
                                @case('pending')
                                    <span class="status-badge bg-warning text-dark">Pending Subject Coordinator</span>
                                    @break
                                @case('coordinator_approved')
                                    <span class="status-badge bg-info text-white">Pending Head of Office</span>
                                    @break
                                @case('final_approved')
                                    @if($request->producer)
                                        <span class="status-badge bg-primary">Produced by {{ ucwords(str_replace('_', ' ', $request->producer)) }}</span>
                                    @else
                                        <span class="status-badge bg-success">Approved</span>
                                    @endif
                                    @break
                                @case('produced')
                                    <span class="status-badge bg-primary">Produced by {{ ucwords(str_replace('_', ' ', $request->producer)) }}</span>
                                    @break
                                @case('rejected')
                                    <span class="status-badge bg-danger">Rejected</span>
                                    @break
                                @default
                                    <span class="status-badge bg-secondary">{{ ucwords(str_replace('_', ' ', $request->status)) }}</span>
                            @endswitch
                        </td>
                        <td>{{ $request->comments }}</td>
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
</body>
</html>
