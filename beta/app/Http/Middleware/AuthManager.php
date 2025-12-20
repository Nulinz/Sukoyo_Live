<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AuthManager
{
    public function handle(Request $request, Closure $next)
    {
        if (!Session::has('loginId') || Session::get('role') !== 'manager') {
            return redirect()->route('login');
        }

        return $next($request);
    }
}