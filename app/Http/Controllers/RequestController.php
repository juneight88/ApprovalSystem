<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request as HttpRequest;
use App\Models\Request;
use App\Models\User;
use App\Models\Subject;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Session;
use App\Services\DocumentGeneratorService;
use App\Services\ApprovalRoutingService;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ApprovedRequestsExport;
use App\Exports\RequestExport;
use Illuminate\Support\Facades\Storage;

class RequestController extends Controller
{
    protected $approvalRoutingService;

    public function __construct(ApprovalRoutingService $approvalRoutingService)
    {
        $this->approvalRoutingService = $approvalRoutingService;
    }

    // Show the request form for test questionnaire
    public function showRequestForm()
    {
        $user = Session::get('user');
        
        // Get all subjects
        $subjects = Subject::all();

        return view('request.form', compact('user', 'subjects'));
    }

    // Show the request form for documents
    public function showRequestFormDoc()
    {
        return view('request.doc');
    }

    // Handle the request form submission for test questionnaire
    public function submitTestQuestionnaireRequest(HttpRequest $request)
    {
        $user = Session::get('user');

        // Validate the request input
        $validated = $request->validate([
            'department' => 'required|string',
            'test_category' => 'required|string',
            'date_exam' => 'required|date',
            'modality_of_learning' => 'required|in:Face-to-face,Online',
            'subject_id' => 'required|exists:subjects,id',
            'specific_subject' => 'nullable|string',
            'exam_time_allotment' => 'required|integer',
            'paper_size' => 'required|in:Legal,Short,A4',
            'printing_mode' => 'required|in:Both sides,One side only',
            'number_of_pages' => 'required|integer',
            'number_of_copies' => 'required|integer',
            'file' => 'nullable|mimes:pdf|max:2048',
        ]);

        // Get the subject details
        $subject = Subject::find($validated['subject_id']);

        // Store the uploaded file if present
        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('requests', 'public');
        }

        // Create the request with all required fields
        $newRequest = new Request();
        $newRequest->user_id = $user->id;
        $newRequest->department = $validated['department'];
        $newRequest->test_category = $validated['test_category'];
        $newRequest->date_request = now();
        $newRequest->date_exam = $validated['date_exam'];
        $newRequest->modality_of_learning = $validated['modality_of_learning'];
        $newRequest->subject_id = $validated['subject_id'];
        $newRequest->specific_subject = $validated['specific_subject'];
        $newRequest->exam_time_allotment = $validated['exam_time_allotment'];
        $newRequest->paper_size = $validated['paper_size'];
        $newRequest->printing_mode = $validated['printing_mode'];
        $newRequest->number_of_pages = $validated['number_of_pages'];
        $newRequest->number_of_copies = $validated['number_of_copies'];
        $newRequest->file_path = $filePath;
        $newRequest->status = 'pending';

        // Determine approvers using the service for ALL requests
        $approvers = $this->approvalRoutingService->determineApprovers($subject, $user);
        
        // Set the approvers
        $newRequest->coordinator_id = $approvers['coordinator'] ? $approvers['coordinator']->id : null;
        $newRequest->head_office_id = $approvers['head_office'] ? $approvers['head_office']->id : null;

        // Log the approval routing for debugging
        \Log::info('Request Submission', [
            'subject' => $subject->toArray(),
            'approvers' => $approvers,
            'coordinator_id' => $newRequest->coordinator_id,
            'head_office_id' => $newRequest->head_office_id
        ]);

        $newRequest->save();

        return redirect()->route('dashboard')->with('success', 'Request submitted successfully.');
    }

    // Handle the document request form submission
    public function submitDocumentRequest(HttpRequest $request)
    {
        $user = Session::get('user');

        // Validate the request input for document-related form
        $validated = $request->validate([
            'date' => 'required|date',
            'type_of_document' => 'required|string',
            'other_document' => 'nullable|string', // For "Others" option
            'date_required' => 'required|date',
            'paper_size' => 'required|string',
            'mode' => 'required|string',
            'number_of_pages' => 'required|integer',
            'number_of_copies' => 'required|integer',
            'file' => 'nullable|mimes:pdf|max:2048',
        ]);

        // Store the uploaded file if present
        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('requests', 'public');
        }

        // Create the request for document-related form
        Request::create([
            'date_request' => $validated['date'],
            'type_of_document' => $validated['type_of_document'],
            'other_document' => $validated['other_document'] ?? null,
            'date_required' => $validated['date_required'],
            'paper_size' => $validated['paper_size'],
            'mode' => $validated['mode'],
            'number_of_pages' => $validated['number_of_pages'],
            'number_of_copies' => $validated['number_of_copies'],
            'file' => $filePath,
            'user_id' => $user->id,
            'status' => 'pending',
        ]);

        return redirect()->route('dashboard')->with('success', 'Document request submitted successfully!');
    }

    // View all the user's request history
    public function requestHistory()
    {
        $user = Session::get('user'); // Get logged-in user
    
        // Fetch all requests for this user
        $requests = \App\Models\Request::where('user_id', $user->id)->get();
    
        return view('request.history', compact('requests'));
    }

    // Manage requests that need approval
    public function manageRequests()
    {
        $user = Session::get('user');
        $requests = collect();

        // Log the current user details for debugging
        \Log::info('User attempting to manage requests', [
            'user_id' => $user->id,
            'role' => $user->role,
            'department' => $user->department
        ]);

        if ($user->role === 'Subject Coordinator') {
            if ($user->department === 'BASIC EDUCATION') {
                $subjectHandled = json_decode($user->subject_handled) ?? [$user->subject_handled];
                
                $requests = Request::whereHas('subject', function($query) use ($subjectHandled) {
                    $query->where('department', 'BASIC EDUCATION')
                          ->whereIn('name', (array)$subjectHandled);
                })
                ->where(function($query) {
                    $query->whereIn('status', ['pending', 'rejected', 'coordinator_approved', 'final_approved']);
                })
                ->with(['subject', 'user'])
                ->orderBy('created_at', 'desc')
                ->get();

            } else {
                $requests = Request::where(function($query) use ($user) {
                    $query->where('coordinator_id', $user->id)
                          ->orWhere('subject_id', function($subQuery) use ($user) {
                              $subQuery->select('id')
                                     ->from('subjects')
                                     ->where('coordinator_id', $user->id);
                          });
                })
                ->where(function($query) {
                    $query->whereIn('status', ['pending', 'rejected', 'coordinator_approved', 'final_approved']);
                })
                ->with(['subject', 'user'])
                ->orderBy('created_at', 'desc')
                ->get();
            }
        } elseif ($user->role === 'Head of Office') {
            // For regular Head of Office, show requests from their department that are coordinator approved
            $requests = Request::where(function($query) use ($user) {
                $query->where('head_office_id', $user->id)
                    ->orWhereHas('subject', function($subQuery) use ($user) {
                        $subQuery->where('department', $user->department);
                    });
            })
            ->where('status', 'coordinator_approved') // Only show coordinator approved requests
            ->with(['subject', 'user', 'coordinator'])
            ->orderBy('created_at', 'desc')
            ->get();

            // Log the fetched requests for debugging
            \Log::info('Head of Office Requests', [
                'head_office_id' => $user->id,
                'department' => $user->department,
                'request_count' => $requests->count()
            ]);
        } elseif (in_array($user->role, ['Elementary Head of Office', 'Junior High School Head of Office', 'Senior High School Head of Office'])) {
            if ($user->department === 'BASIC EDUCATION') {
                $levelMap = [
                    'Elementary Head of Office' => 'ELEM',
                    'Junior High School Head of Office' => 'JHS',
                    'Senior High School Head of Office' => 'SHS'
                ];
                
                $educationLevel = $levelMap[$user->role];
                
                // For Basic Education Heads, show all requests for their level
                $requests = Request::whereHas('subject', function($query) use ($educationLevel) {
                    $query->where('department', 'BASIC EDUCATION')
                          ->whereRaw('UPPER(education_level) = ?', [$educationLevel]);
                })
                ->where(function($query) {
                    $query->whereIn('status', ['pending', 'coordinator_approved', 'rejected', 'final_approved']);
                })
                ->with(['subject', 'user', 'coordinator'])
                ->orderBy('created_at', 'desc')
                ->get();

            } else {
                $requests = Request::where(function($query) use ($user) {
                    $query->where('head_office_id', $user->id)
                          ->orWhereHas('subject', function($subQuery) use ($user) {
                              $subQuery->where('department', $user->department);
                          });
                })
                ->where(function($query) {
                    $query->whereIn('status', ['pending', 'coordinator_approved', 'rejected', 'final_approved']);
                })
                ->with(['subject', 'user', 'coordinator'])
                ->orderBy('created_at', 'desc')
                ->get();
            }
        }

        // Log the fetched requests for debugging
        \Log::info('Fetched requests for management', [
            'user_id' => $user->id,
            'request_count' => $requests->count(),
            'requests' => $requests->map(fn($req) => [
                'id' => $req->id,
                'status' => $req->status,
                'subject' => optional($req->subject)->name,
                'department' => optional($req->subject)->department,
                'education_level' => optional($req->subject)->education_level,
                'coordinator_id' => $req->coordinator_id,
                'head_office_id' => $req->head_office_id
            ])->toArray()
        ]);

        return view('request.manage', compact('requests', 'user'));
    }

    // Approve the request by coordinator
    public function approveByCoordinator($id)
    {
        $request = Request::with(['subject', 'coordinator'])->findOrFail($id);
        $user = Session::get('user');

        // Log the approval attempt
        \Log::info('Coordinator Approval Attempt', [
            'request_id' => $id,
            'user_id' => $user->id,
            'user_role' => $user->role,
            'subject_name' => optional($request->subject)->name,
            'subject_department' => optional($request->subject)->department,
            'education_level' => optional($request->subject)->education_level
        ]);

        // Verify coordinator can approve this request
        if ($user->department === 'BASIC EDUCATION') {
            $subjectHandled = json_decode($user->subject_handled) ?? [$user->subject_handled];
            
            if (!in_array($request->subject->name, (array)$subjectHandled)) {
                \Log::warning('Unauthorized Basic Education coordinator approval attempt', [
                    'coordinator_id' => $user->id,
                    'subject_handled' => $subjectHandled,
                    'request_subject' => $request->subject->name
                ]);
                return redirect()->route('manage.requests')
                    ->with('error', 'You are not authorized to approve this request. Subject not in your handled subjects.');
            }
        } else if ($request->coordinator_id !== $user->id) {
            return redirect()->route('manage.requests')
                ->with('error', 'You are not authorized to approve this request.');
        }

        try {
            $request->status = 'coordinator_approved';
            $request->coordinator_approved_at = now();
            $request->save();

            \Log::info('Request Coordinator Approval Successful', [
                'request_id' => $request->id,
                'coordinator_id' => $user->id,
                'subject' => optional($request->subject)->toArray()
            ]);

            return redirect()->route('manage.requests')
                ->with('success', 'Request approved successfully. Awaiting Head of Office approval.');
        } catch (\Exception $e) {
            \Log::error('Error in coordinator approval', [
                'request_id' => $id,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->route('manage.requests')
                ->with('error', 'An error occurred while approving the request. Please try again.');
        }
    }

    // Approve the request by Head of Office
    public function approveByDean($id)
    {
        // Load all necessary relationships for document generation
        $request = Request::with(['subject', 'coordinator', 'user', 'headOffice'])->findOrFail($id);
        $user = Session::get('user');

        // Authorization checks...
        if ($request->status !== 'coordinator_approved') {
            return redirect()->route('manage.requests')
                ->with('error', 'This request needs coordinator approval first.');
        }

        try {
            // Generate the approval sheet first
            $documentGenerator = app(DocumentGeneratorService::class);
            $path = $documentGenerator->generateRequestDocument($request);
            
            if (!$path) {
                \Log::error('Document generation failed - no path returned', [
                    'request_id' => $id
                ]);
                return redirect()->route('manage.requests')
                    ->with('error', 'Failed to generate approval sheet. Please try again.');
            }

            // Update request status and path
            $request->status = 'final_approved';
            $request->head_office_approved_at = now();
            $request->generated_document_path = $path;
            $request->save();

            // Verify the document exists
            if (!Storage::disk('public')->exists($path)) {
                \Log::error('Document not found after generation', [
                    'request_id' => $id,
                    'path' => $path
                ]);
                return redirect()->route('manage.requests')
                    ->with('error', 'Failed to save approval sheet. Please try again.');
            }

            return redirect()->route('manage.requests')
                ->with('success', 'Request has been approved and approval sheet generated.');

        } catch (\Exception $e) {
            \Log::error('Error in final approval', [
                'request_id' => $id,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->route('manage.requests')
                ->with('error', 'An error occurred while approving the request: ' . $e->getMessage());
        }
    }

    // Reject the request
    public function rejectRequest($id)
    {
        $request = Request::find($id);
        $user = Session::get('user');

        // Verify user has permission to reject this request
        if ($request->coordinator_id !== $user->id && $request->head_office_id !== $user->id) {
            return redirect()->route('manage.requests')->with('error', 'You are not authorized to reject this request.');
        }

        $request->status = 'rejected';
        $request->rejected_at = now();
        $request->rejected_by = $user->id;
        $request->save();

        return redirect()->route('manage.requests')->with('success', 'Request rejected successfully.');
    }

    // Add a comment to a request
    public function addComment(HttpRequest $request, $id)
    {
        $user = Session::get('user');
        
        // Validate comment
        $validated = $request->validate([
            'comment' => 'required|string|max:1000'
        ]);

        $requestItem = Request::findOrFail($id);
        
        // Format the new comment with timestamp and user
        $newComment = sprintf(
            "[%s] %s: %s",
            now()->format('Y-m-d H:i:s'),
            $user->username,
            $validated['comment']
        );

        // Append the new comment to existing comments
        $currentComments = $requestItem->comments ?? '';
        $requestItem->comments = $currentComments 
            ? $currentComments . "\n" . $newComment
            : $newComment;
        
        $requestItem->commented_at = now();
        $requestItem->commented_by = $user->id;
        $requestItem->save();

        return back()->with('success', 'Comment added successfully.');
    }

    public function exportRequests()
    {
        $requests = Request::all();
        
        // Create a new Spreadsheet object
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Add the headers to the spreadsheet
        $sheet->setCellValue('A1', 'Department');
        $sheet->setCellValue('B1', 'Test Category');
        $sheet->setCellValue('C1', 'Date Request');
        $sheet->setCellValue('D1', 'Type of Document');
        $sheet->setCellValue('E1', 'Subject');
        $sheet->setCellValue('F1', 'Modality');
        $sheet->setCellValue('G1', 'Mode');
        $sheet->setCellValue('H1', 'Exam Date');
        $sheet->setCellValue('I1', 'Time Allotment');
        $sheet->setCellValue('J1', 'No. of Pages');
        $sheet->setCellValue('K1', 'No. of Copies');
        $sheet->setCellValue('L1', 'File');
        $sheet->setCellValue('M1', 'Status');

        // Start adding the request data to rows below headers
        $row = 2; // Start at row 2 since row 1 is for headers
        foreach ($requests as $request) {
            $sheet->setCellValue('A' . $row, $request->department);
            $sheet->setCellValue('B' . $row, $request->test_category);
            $sheet->setCellValue('C' . $row, $request->date_request);
            $sheet->setCellValue('D' . $row, $request->type_of_document);
            $sheet->setCellValue('E' . $row, $request->subject);
            $sheet->setCellValue('F' . $row, $request->modality_of_learning);
            $sheet->setCellValue('G' . $row, $request->mode);
            $sheet->setCellValue('H' . $row, $request->date_exam_required);
            $sheet->setCellValue('I' . $row, $request->exam_time_allotment);
            $sheet->setCellValue('J' . $row, $request->number_of_pages);
            $sheet->setCellValue('K' . $row, $request->number_of_copies);
            $sheet->setCellValue('L' . $row, $request->file ? 'Available' : 'No file');
            $sheet->setCellValue('M' . $row, $request->status);
            $row++;
        }

        // Create the Xlsx writer
        $writer = new Xlsx($spreadsheet);

        // Set the filename for the download
        $filename = 'request_history.xlsx';

        // Output the file as a download response
        return response()->stream(
            function () use ($writer) {
                $writer->save('php://output');
            },
            200,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => "attachment; filename=\"$filename\"",
            ]
        );
    }

    public function exportApprovedRequests()
    {
        return Excel::download(new RequestExport, 'approved_requests.xlsx');
    }

    public function markRequestComplete($id)
    {
        $request = Request::findOrFail($id);
        $request->status = 'completed';
        $request->save();
        
        return response()->json(['message' => 'Request marked as complete']);
    }

    public function assignProducer(HttpRequest $request, $id)
    {
        $requestModel = Request::findOrFail($id);
        
        // Check if request is in final_approved state
        if ($requestModel->status !== 'final_approved') {
            return back()->with('error', 'Only final approved requests can be assigned a producer.');
        }

        // Update producer and set status to produced
        $requestModel->setProducer($request->producer);
        
        return back()->with('success', 'Producer assigned successfully.');
    }

    public function viewApprovalSheet($id)
    {
        $request = Request::find($id);
        
        if (!$request || !$request->generated_document_path) {
            return redirect()->back()->with('error', 'Approval sheet not found.');
        }

        return response()->file(storage_path('app/public/' . $request->generated_document_path));
    }

    public function updateClaimants(HttpRequest $request, $id)
    {
        $requestModel = \App\Models\Request::findOrFail($id);
        $requestModel->update([
            'claimants' => $request->claimants
        ]);
        
        return back()->with('success', 'Claimants updated successfully');
    }

    public function editRequest($id)
    {
        $user = Session::get('user');
        $request = Request::findOrFail($id);
        
        // Check if request belongs to user and is rejected
        if ($request->user_id !== $user->id || $request->status !== 'rejected') {
            return redirect()->route('request.history')
                           ->with('error', 'You can only edit your own rejected requests.');
        }

        // Get all subjects for the form
        $subjects = Subject::all();

        return view('request.edit', compact('request', 'user', 'subjects'));
    }

    public function resubmitRequest(HttpRequest $request, $id)
    {
        $user = Session::get('user');
        $requestModel = Request::findOrFail($id);
        
        // Check if request belongs to user and is rejected
        if ($requestModel->user_id !== $user->id || $requestModel->status !== 'rejected') {
            return redirect()->route('request.history')
                           ->with('error', 'You can only resubmit your own rejected requests.');
        }

        // Validate the request input
        $validated = $request->validate([
            'department' => 'required|string',
            'test_category' => 'required|string',
            'date_exam' => 'required|date',
            'modality_of_learning' => 'required|in:Face-to-face,Online',
            'subject_id' => 'required|exists:subjects,id',
            'specific_subject' => 'nullable|string',
            'exam_time_allotment' => 'required|integer',
            'paper_size' => 'required|in:Legal,Short,A4',
            'printing_mode' => 'required|in:Both sides,One side only',
            'number_of_pages' => 'required|integer',
            'number_of_copies' => 'required|integer',
            'file' => 'nullable|mimes:pdf|max:2048',
        ]);

        // Store the uploaded file if present
        if ($request->hasFile('file')) {
            // Delete old file if exists
            if ($requestModel->file_path) {
                Storage::disk('public')->delete($requestModel->file_path);
            }
            $filePath = $request->file('file')->store('requests', 'public');
            $requestModel->file_path = $filePath;
        }

        // Update the request with new values
        $requestModel->fill($validated);
        $requestModel->status = 'pending'; // Reset status to pending
        $requestModel->date_request = now();
        
        // Determine approvers using the service
        $subject = Subject::find($validated['subject_id']);
        $approvers = $this->approvalRoutingService->determineApprovers($subject, $user);
        
        // Set the approvers
        $requestModel->coordinator_id = $approvers['coordinator'] ? $approvers['coordinator']->id : null;
        $requestModel->head_office_id = $approvers['head_office'] ? $approvers['head_office']->id : null;

        $requestModel->save();

        return redirect()->route('request.history')
                        ->with('success', 'Request has been resubmitted successfully.');
    }
}
