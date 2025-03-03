<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\User;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // Validate login input
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);
    
        // Find the user by username and password
        $user = User::where('username', $request->username)
                    ->where('password', $request->password) // No hashing, as per your request
                    ->first();
    
        if ($user) {
            // Store user in session
            Session::put('user', $user);
    
            // Check if profile needs setup for all users except super_admin and Admin
            if (!$user->setup_complete && !in_array($user->role, ['super_admin', 'Admin'])) {
                return redirect('/setup-profile')->with('message', 'Please complete your profile setup.');
            }

            // Redirect based on role
            switch($user->role) {
                case 'super_admin':
                    return redirect()->route('superAdmin')->with('success', 'Welcome, Super Admin!');
                case 'Admin':
                    return redirect()->route('admin.dashboard')->with('success', 'Welcome, Admin!');
                case 'Head of Office':
                    return redirect()->route('head.office.dashboard')->with('success', 'Welcome!');
                case 'Subject Coordinator':
                    return redirect()->route('coordinator.dashboard')->with('success', 'Welcome!');
                default:
                    return redirect()->route('dashboard')->with('success', 'Login successful!');
            }
        }
    
        return back()->with('error', 'Invalid username or password.');
    }
    
    public function logout()
    {
        Session::forget('user');
        return redirect('/login');
    }

}
