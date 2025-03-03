<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB; // Added this line
use App\Models\User;
use App\Models\Subject; // To fetch subjects
use App\Models\Request as Req; // Added this line

class UserController extends Controller
{
    
    public function setupProfile()
{
    $user = Session::get('user');
    $departments = [
        'CAS' => 'College of Arts and Sciences',
        'CBM' => 'College of Business and Management',
        'CCIS' => 'College of Computing and Information Science',
    ];

    return view('auth.setup-profile', ['user' => $user, 'departments' => $departments]);
}

public function saveProfile(Request $request)
{
    $user = Session::get('user');

    if (!$user) {
        return redirect('/login')->with('error', 'Session expired. Please login again.');
    }

    // Validate the request
    $request->validate([
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        'signature' => 'nullable|image|mimes:png|max:2048'
    ]);

    // Find the user in the database
    $dbUser = User::find($user->id);
    if (!$dbUser) {
        return redirect('/login')->with('error', 'User not found. Please login again.');
    }

    // Update basic information
    $dbUser->first_name = $request->first_name;
    $dbUser->last_name = $request->last_name;

    // Handle profile picture upload
    if ($request->hasFile('profile_picture')) {
        $profilePath = $request->file('profile_picture')->store('profile-pictures', 'public');
        $dbUser->profile_picture = $profilePath;
    }

    // Handle signature upload
    if ($request->hasFile('signature')) {
        try {
            $signaturePath = $request->file('signature')->store('signatures', 'public');
            $dbUser->signature = $signaturePath;
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to upload signature. Please try again.');
        }
    }

    $dbUser->setup_complete = true;
    
    try {
        $dbUser->save();
        // Update session with new user data
        Session::put('user', $dbUser);
        
        // Redirect based on role to avoid double redirect
        switch ($dbUser->role) {
            case 'super_admin':
                return redirect()->route('superAdmin')->with('success', 'Profile updated successfully');
            case 'Admin':
                return redirect()->route('admin.dashboard')->with('success', 'Profile updated successfully');
            case 'Head of Office':
                return redirect()->route('head.office.dashboard')->with('success', 'Profile updated successfully');
            case 'Subject Coordinator':
                return redirect()->route('coordinator.dashboard')->with('success', 'Profile updated successfully');
            default:
                return redirect()->route('dashboard')->with('success', 'Profile updated successfully');
        }
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Failed to save profile. Please try again.');
    }
}

public function edit($id)
{
    $user = User::find($id);
    $userData = $user->toArray();
    return response()->json($userData);
}

// Update the user's username and password
public function update(Request $request, $id)
{
    $request->validate([
        'username' => 'required|string|unique:users,username,'.$id,
        'password' => 'required|string',
        'department' => 'required|string',
        'role' => 'required|string',
        'program' => 'nullable|string',
        'subject_handled' => 'nullable|string'
    ]);

    $user = User::find($id);
    $oldRole = $user->role;
    $oldDepartment = $user->department;
    $oldProgram = $user->program;

    $user->username = $request->username;
    $user->password = $request->password;
    $user->department = $request->department;
    $user->role = $request->role;
    $user->program = $request->program;
    $user->subject_handled = ($request->department === 'BASIC EDUCATION' && $request->subject_handled) 
        ? json_encode([$request->subject_handled]) 
        : null;
    $user->save();

    // Handle Subject Coordinator role changes
    if ($oldRole === 'Subject Coordinator') {
        // Remove old subject assignments
        Subject::where('coordinator_id', $user->id)->update(['coordinator_id' => null]);
    }

    if ($request->role === 'Subject Coordinator') {
        // Clear any existing coordinator assignments for this department/program
        $query = Subject::where('department', $request->department);
        
        if ($request->department === 'BASIC EDUCATION' && $request->subject_handled) {
            // For Basic Education, assign subjects based on subject handled if provided
            $query->where('subject_code', $request->subject_handled);
        } else {
            // For other departments
            if ($request->program) {
                $query->where('program', $request->program);
            }
        }
        
        // Clear existing assignments
        $query->where('coordinator_id', '!=', $user->id)->update(['coordinator_id' => null]);
        
        // Assign new coordinator
        $query->update(['coordinator_id' => $user->id]);
    }

    return redirect()->route('superAdmin')->with('success', 'User updated successfully.');
}   

// Delete the user
public function destroy($id)
{
    $user = User::find($id);
    
    // Remove subject coordinator assignments before deleting
    if ($user->role === 'Subject Coordinator') {
        Subject::where('coordinator_id', $user->id)->update(['coordinator_id' => null]);
    }
    
    $user->delete();

    return redirect()->route('superAdmin')->with('success', 'User deleted successfully.');
}

public function superAdminDashboard()
{
    $users = User::all();
    $departments = [
        'CCIS' => 'College of Computing and Information Science',
        'CTE' => 'College of Teacher Education',
        'CAS' => 'College of Arts and Sciences'
    ];
    $roles = [
        'Personnel' => 'Personnel',
        'Non-teaching personnel' => 'Non-teaching personnel',
        'Admin' => 'Admin',
        'Head of Office' => 'Head of Office',
        'Subject Coordinator' => 'Subject Coordinator'
    ];
    return view('dashboard.superAdmin', ['users' => $users, 'departments' => $departments, 'roles' => $roles]);
}
public function store(Request $request)
{
    // Keep the current user session
    $currentUser = Session::get('user');
    
    $rules = [
        'username' => 'required|string|unique:users,username',
        'password' => 'required|string',
        'department' => 'required|string',
        'role' => 'required|string',
        'program' => 'nullable|string',
        'subject_handled' => 'nullable|string'
    ];

    // Make program required only for Basic Education Head of Office
    if ($request->department === 'BASIC EDUCATION' && $request->role === 'Head of Office') {
        $rules['program'] = 'required|string';
    }
    // Make program required for Subject Coordinator
    else if ($request->role === 'Subject Coordinator') {
        $rules['program'] = 'required|string';
    }

    $request->validate($rules);

    $user = new User();
    $user->username = $request->username;
    $user->password = $request->password;
    $user->department = $request->department;
    $user->role = $request->role;
    $user->program = $request->program;
    $user->subject_handled = ($request->department === 'BASIC EDUCATION' && $request->subject_handled) 
        ? json_encode([$request->subject_handled]) 
        : null;
    
    // Set setup_complete based on role
    if (in_array($user->role, ['super_admin', 'Admin'])) {
        $user->setup_complete = true;
    } else {
        $user->setup_complete = false;
    }
    
    $user->save();

    // Handle Subject Coordinator role
    if ($request->role === 'Subject Coordinator') {
        $query = Subject::where('department', $request->department);
        
        if ($request->department === 'BASIC EDUCATION' && $request->subject_handled) {
            // For Basic Education, assign subjects based on subject handled if provided
            $query->where('subject_code', $request->subject_handled);
        } else {
            // For other departments
            if ($request->program) {
                $query->where('program', $request->program);
            }
        }
        
        // Clear existing assignments
        $query->where('coordinator_id', '!=', $user->id)->update(['coordinator_id' => null]);
        
        // Assign new coordinator
        $query->update(['coordinator_id' => $user->id]);
    }

    // Put the current user back in session to prevent logout
    Session::put('user', $currentUser);

    return redirect()->route('superAdmin')->with('success', 'User added successfully.');
}

    // Show the dashboard based on user role
    public function userDashboard()
    {
        $user = Session::get('user');
        
        if (!$user) {
            return redirect('/login');
        }

        // Get request statistics
        $pendingCount = Req::where('user_id', $user->id)
                          ->whereIn('status', ['pending', 'coordinator_approved'])
                          ->count();

        $approvedCount = Req::where('user_id', $user->id)
                           ->where('status', 'final_approved')
                           ->count();

        $rejectedCount = Req::where('user_id', $user->id)
                           ->where('status', 'rejected')
                           ->count();

        $totalCount = $pendingCount + $approvedCount + $rejectedCount;

        // Get recent requests
        $recentRequests = Req::where('user_id', $user->id)
                            ->orderBy('date_request', 'desc')
                            ->take(5)
                            ->get();

        return view('dashboard.personnel', compact(
            'user',
            'pendingCount',
            'approvedCount',
            'rejectedCount',
            'totalCount',
            'recentRequests'
        ));
    }

    public function adminDashboard()
    {
        $user = Session::get('user');
        
        if (!$user || $user->role !== 'Admin') {
            return redirect('/dashboard');
        }

        // Get all final approved requests
        $approvedRequests = Req::where('status', 'final_approved')
                             ->orderBy('updated_at', 'desc')
                             ->get();

        // Get counts for statistics
        $approvedCount = $approvedRequests->count();
        $adminCount = Req::where('status', 'final_approved')
                        ->where('producer', 'admin')
                        ->count();
        $assistantCount = Req::where('status', 'final_approved')
                            ->where('producer', 'student_assistant')
                            ->count();

        return view('dashboard.admin', compact(
            'user',
            'approvedRequests',
            'approvedCount',
            'adminCount',
            'assistantCount'
        ));
    }

    public function assignProducer(Request $request, $id)
    {
        $user = Session::get('user');
        
        if (!$user || $user->role !== 'Admin') {
            return redirect('/dashboard');
        }

        $testRequest = Req::findOrFail($id);
        
        if ($testRequest->status !== 'final_approved') {
            return back()->with('error', 'Only final approved requests can be assigned a producer.');
        }

        $testRequest->producer = $request->producer;
        $testRequest->save();

        return back()->with('success', 'Producer assigned successfully.');
    }

    public function dashboard()
    {
        $user = Session::get('user');

        if (!$user) {
            return redirect('/login');
        }

        // Redirect based on role
        switch ($user->role) {
            case 'super_admin':
                return redirect()->route('superAdmin');
            case 'Admin':
                return redirect()->route('admin.dashboard');
            case 'Head of Office':
                return redirect()->route('head.office.dashboard');
            case 'Subject Coordinator':
                return redirect()->route('coordinator.dashboard');
            case 'Personnel':
            case 'Non-teaching personnel':
                return $this->userDashboard();
            default:
                return $this->userDashboard();
        }
    }

    public function headOfficeDashboard()
    {
        $user = Session::get('user');
        
        if (!$user || $user->role !== 'Head of Office') {
            return redirect('/dashboard');
        }

        // Get requests that need head's approval
        $pendingCount = Req::where('head_office_id', $user->id)
                          ->where('status', 'coordinator_approved')
                          ->count();

        $approvedCount = Req::where('head_office_id', $user->id)
                           ->where('status', 'final_approved')
                           ->count();

        $rejectedCount = Req::where('head_office_id', $user->id)
                           ->where('status', 'rejected')
                           ->count();

        $totalCount = $pendingCount + $approvedCount + $rejectedCount;

        // Get recent pending requests
        $recentRequests = Req::where('head_office_id', $user->id)
                             ->where('status', 'coordinator_approved')
                             ->orderBy('date_request', 'desc')
                             ->take(5)
                             ->get();

        return view('dashboard.headOfOffice', compact(
            'user',
            'pendingCount',
            'approvedCount',
            'rejectedCount',
            'totalCount',
            'recentRequests'
        ));
    }

    public function coordinatorDashboard()
    {
        $user = Session::get('user');
        
        if (!$user || $user->role !== 'Subject Coordinator') {
            return redirect('/dashboard');
        }

        // Get requests that need coordinator's approval
        $pendingCount = Req::where('coordinator_id', $user->id)
                          ->where('status', 'pending')
                          ->count();

        $approvedCount = Req::where('coordinator_id', $user->id)
                           ->where('status', 'coordinator_approved')
                           ->count();

        $rejectedCount = Req::where('coordinator_id', $user->id)
                           ->where('status', 'rejected')
                           ->count();

        $totalCount = $pendingCount + $approvedCount + $rejectedCount;

        // Get recent pending requests
        $recentRequests = Req::where('coordinator_id', $user->id)
                            ->where('status', 'pending')
                            ->orderBy('date_request', 'desc')
                            ->take(5)
                            ->get();

        return view('dashboard.coordinator', compact(
            'user',
                'pendingCount',
                'approvedCount',
                'rejectedCount',
                'totalCount',
                'recentRequests'
            ));
    }

    public function updateProfile(Request $request)
    {
        $user = Session::get('user');
        
        if (!$user) {
            return redirect()->back()->with('error', 'User not found in session');
        }

        // Validate the request
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'signature' => 'nullable|image|mimes:png|max:2048'
        ]);

        // Update basic information
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            $profilePath = $request->file('profile_picture')->store('profile-pictures', 'public');
            $user->profile_picture = $profilePath;
        }

        // Handle signature upload
        if ($request->hasFile('signature')) {
            $signaturePath = $request->file('signature')->store('signatures', 'public');
            $user->signature = $signaturePath;
        }

        $user->save();
        
        // Update the session with the new user data
        Session::put('user', $user);

        return redirect()->back()->with('success', 'Profile updated successfully');
    }
}
