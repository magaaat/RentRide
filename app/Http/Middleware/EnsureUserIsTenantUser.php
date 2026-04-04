<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsTenantUser
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        abort_unless($user && $user->isTenantUser(), 403);
        if ($user->isStaff() && ! $user->is_active) {
            abort(403, 'Your staff account is disabled.');
        }

        return $next($request);
    }
}

