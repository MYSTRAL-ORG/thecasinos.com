<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class PasswordProtectMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        Session::put('adminUri', $request->getRequestUri());
        // Check if the session does not have 'isPasswordProtectedRouteAuthenticated'
        if (!  Session::get('isPasswordProtectedRouteAuthenticated', false)) {
            // Redirect to a password prompt route

            return redirect('passwordprompt');
        }

        return $next($request);
    }
}
