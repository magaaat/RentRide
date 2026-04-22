<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\PlanExtensionRequest;
use App\Models\Tenant;
use App\Models\Vehicle;
use App\Support\PlanLimits;
use App\Support\TenantRuntimeVersion;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function superAdmin()
    {
        abort_unless(auth()->user()?->isSuperAdmin(), 403);

        $totalTenants = Tenant::count();
        $activeSubscriptions = Tenant::whereDate('subscription_expiry', '>=', now())->count();
        $platformRevenue = Payment::where('payment_status', 'paid')->sum('amount');

        return view('superadmin.dashboard', compact(
            'totalTenants',
            'activeSubscriptions',
            'platformRevenue'
        ));
    }

    public function admin()
    {
        $tenantId = Auth::user()->tenant_id;

        $totalVehicles = Vehicle::where('tenant_id', $tenantId)->count();
        $activeBookings = Booking::where('tenant_id', $tenantId)
            ->whereIn('status', ['pending', 'confirmed'])
            ->count();
        $revenue = Payment::where('tenant_id', $tenantId)
            ->where('payment_status', 'paid')
            ->sum('amount');

        $pendingBookingsCount = Booking::where('tenant_id', $tenantId)
            ->where('status', 'pending')
            ->count();
        $pendingExtensionCount = PlanExtensionRequest::where('tenant_id', $tenantId)
            ->where('status', 'pending')
            ->count();

        $tenant = Auth::user()->tenant;
        $plan = $tenant->subscription_plan ?? 'basic';
        $monthlyBookingsUsed = PlanLimits::monthlyBookingsCreatedCount((int) $tenantId);
        $monthlyBookingLimit = PlanLimits::monthlyBookingCreationLimit($plan);
        $vehicleLimit = PlanLimits::vehicleLimit($plan);

        return view('admin.dashboard', compact(
            'totalVehicles',
            'activeBookings',
            'revenue',
            'pendingBookingsCount',
            'pendingExtensionCount',
            'plan',
            'monthlyBookingsUsed',
            'monthlyBookingLimit',
            'vehicleLimit'
        ));
    }

    public function updatedModule()
    {
        $user = Auth::user();
        abort_unless($user?->isTenantUser() && $user->tenant_id, 403);

        $tenant = $user->tenant;
        abort_unless($tenant, 403);

        $minimumVersion = (string) config('rentride.update_test_module_min_version', 'v1.0.3');
        $runtimeVersion = TenantRuntimeVersion::currentForTenant(
            tenantId: (int) $tenant->id,
            fallbackVersion: (string) config('rentride.version', '')
        );
        abort_unless(TenantRuntimeVersion::isAtLeast($runtimeVersion, $minimumVersion), 403);

        return view('admin.updated-module', [
            'tenant' => $tenant,
            'runtimeVersion' => $runtimeVersion,
            'runtimeAppliedAt' => TenantRuntimeVersion::appliedAtForTenant((int) $tenant->id),
            'minimumVersion' => $minimumVersion,
        ]);
    }

}

