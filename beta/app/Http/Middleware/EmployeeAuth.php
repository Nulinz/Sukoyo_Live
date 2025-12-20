<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class EmployeeAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if user is logged in
        if (!Session::has('loginId')) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        // Check if user is an employee
        if (Session::get('role') !== 'employee') {
            return redirect()->route('login')->with('error', 'Unauthorized access');
        }

        return $next($request);
    }
}