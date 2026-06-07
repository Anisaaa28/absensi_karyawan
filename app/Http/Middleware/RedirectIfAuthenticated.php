<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     * Redirect to dashboard if user is already logged in (via session).
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        // Check custom session-based auth
        if ($request->session()->has('user_id')) {
            return redirect()->route('dashboard');
        }

        // Also check Laravel's standard auth guards
        $guards = empty($guards) ? [null] : $guards;
        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                return redirect()->route('dashboard');
            }
        }

        return $next($request);
    }
}
