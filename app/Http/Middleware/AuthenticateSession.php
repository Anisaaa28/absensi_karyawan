<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class AuthenticateSession
{
    public function handle(Request $request, Closure $next)
    {
        if (! $request->session()->has('user_id')) {
            return Redirect::route('login');
        }

        if (! Auth::check()) {
            Auth::loginUsingId($request->session()->get('user_id'));
        }

        return $next($request);
    }
}
