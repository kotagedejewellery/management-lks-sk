<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePasswordChanged
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->must_change_password || in_array($request->route()?->getName(), [
            'api.lks.dashboard',
            'api.lks.account.show',
            'api.lks.account.password.update',
        ], true)) {
            return $next($request);
        }

        abort(403, 'Ganti password sementara Anda terlebih dahulu melalui menu Akun Saya.');
    }
}
