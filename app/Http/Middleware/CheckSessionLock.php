<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckSessionLock
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && session('locked') === true) {
            // Prevent infinite loop if already on locked route
            if ($request->is('locked') || $request->is('api/auth/unlock') || $request->is('api/auth/logout')) {
                return $next($request);
            }

            // Return JSON error for AJAX requests
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['error' => 'Session locked'], 403);
            }

            return redirect()->route('locked');
        }

        return $next($request);
    }
}
