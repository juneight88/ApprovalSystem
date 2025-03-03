<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AuthCheck
{
    public function handle(Request $request, Closure $next)
{
    if (!Session::has('user')) {
        \Log::info('No user session found. Redirecting to login.');
        return redirect('/login')->with('error', 'You must log in first.');
    }

    return $next($request);
}

}

