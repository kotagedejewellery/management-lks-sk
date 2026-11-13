<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class ThrottlePasswordResetRequests
{
    public function handle(Request $request, Closure $next)
    {
        if (! $request->isMethod('POST') || $request->route()?->getName() !== 'password.email') {
            return $next($request);
        }

        if (! RateLimiter::attempt('password-reset-ip:'.$request->ip(), 5, fn () => true, 60)) {
            return back()->withInput($request->only('email'))->with('status', 'passwords.throttled');
        }

        return $next($request);
    }
}
