<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureTenantHasFeature
{
    /**
     * Ensure the authenticated admin tenant has a feature enabled.
     *
     * Usage: ->middleware('tenant.feature:payment_tracking')
     */
    public function handle(Request $request, Closure $next, string $feature)
    {
        $user = $request->user();

        if (!$user) {
            abort(401);
        }

        // Super admins bypass tenant feature gating.
        if (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
            return $next($request);
        }

        $tenant = $user->tenant;

        if (!$tenant) {
            abort(403);
        }

        if (!method_exists($tenant, 'hasFeature') || !$tenant->hasFeature($feature)) {
            return redirect()
                ->route('admin.dashboard')
                ->withErrors(['plan' => 'Your current subscription plan does not include this feature.']);
        }

        return $next($request);
    }
}

