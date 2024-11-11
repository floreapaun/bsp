<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckAdmin
{
    public function handle($request, Closure $next)
    {
        // Check if the user is authenticated and is an administrator
        if (Auth::check() && Auth::user()->is_admin) { // Assuming 'is_admin' is the field in your users table
            return $next($request);
        }

        // Redirect to dashboard
        return redirect('/dashboard')->with('error', 'You do not have admin access.');
    }
}
