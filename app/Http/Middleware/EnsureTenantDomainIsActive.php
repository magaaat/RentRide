<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantDomainIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Only enforce for tenant-bound users (rental-company accounts).
        if (! $user || ! $user->tenant_id || $user->isSuperAdmin() || $user->isCustomer()) {
            return $next($request);
        }

        $tenant = $user->tenant;
        if (! $tenant) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => 'Tenant account is no longer available.',
            ]);
        }

        $expired = $tenant->subscription_expiry && now()->greaterThan($tenant->subscription_expiry);
        if ($expired && $tenant->is_domain_active) {
            $tenant->update(['is_domain_active' => false]);
        }

        if ($tenant->status !== 'approved' || ! $tenant->is_domain_active || $expired) {
            $tenantKey = $tenant->slug ?: $user->tenant_id;

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login', ['tenant' => $tenantKey])->withErrors([
                'email' => 'Your company domain is currently disabled. Please contact Super Admin or renew your plan.',
            ]);
        }

        return $next($request);
    }
}
