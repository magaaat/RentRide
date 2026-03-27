<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\Vehicle;
use App\Support\PlanLimits;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TenantReportController extends TenantControllerBase
{
    public function index()
    {
        $tenantId = $this->tenantId();
        $tenant = Auth::user()->tenant;
        $plan = $tenant->subscription_plan ?? 'basic';

        $bookingsTotal = Booking::where('tenant_id', $tenantId)->count();
        $bookingsByStatus = Booking::where('tenant_id', $tenantId)
            ->selectRaw('status, COUNT(*) as c')
            ->groupBy('status')
            ->pluck('c', 'status');

        $revenueTotal = Payment::where('tenant_id', $tenantId)
            ->where('payment_status', 'paid')
            ->sum('amount');

        $monthlyUsed = PlanLimits::monthlyBookingsCreatedCount((int) $tenantId);
        $monthlyLimit = PlanLimits::monthlyBookingCreationLimit($plan);

        $vehiclesCount = Vehicle::where('tenant_id', $tenantId)->count();
        $vehicleCap = PlanLimits::vehicleLimit($plan);

        $thisMonthStart = now()->startOfMonth();
        $thisMonthEnd = now()->endOfMonth();
        $bookingsThisMonth = Booking::where('tenant_id', $tenantId)
            ->whereBetween('created_at', [$thisMonthStart, $thisMonthEnd])
            ->count();

        return view('admin.reports', compact(
            'plan',
            'bookingsTotal',
            'bookingsByStatus',
            'revenueTotal',
            'monthlyUsed',
            'monthlyLimit',
            'vehiclesCount',
            'vehicleCap',
            'bookingsThisMonth'
        ));
    }

    public function analytics()
    {
        $tenantId = $this->tenantId();

        $from = now()->subMonths(5)->startOfMonth();

        $revenueByMonth = Payment::query()
            ->where('tenant_id', $tenantId)
            ->where('payment_status', 'paid')
            ->where('created_at', '>=', $from)
            ->get(['amount', 'created_at'])
            ->groupBy(fn ($p) => $p->created_at->format('Y-m'))
            ->map(fn ($group) => $group->sum('amount'))
            ->sortKeys();

        $bookingsByMonth = Booking::query()
            ->where('tenant_id', $tenantId)
            ->where('created_at', '>=', $from)
            ->get(['created_at'])
            ->groupBy(fn ($b) => $b->created_at->format('Y-m'))
            ->map(fn ($group) => $group->count())
            ->sortKeys();

        $topVehicles = Booking::query()
            ->where('tenant_id', $tenantId)
            ->select('vehicle_id', DB::raw('COUNT(*) as booking_count'))
            ->groupBy('vehicle_id')
            ->orderByDesc('booking_count')
            ->limit(5)
            ->get()
            ->load('vehicle');

        $avgBookingValue = Payment::where('tenant_id', $tenantId)
            ->where('payment_status', 'paid')
            ->avg('amount');

        return view('admin.analytics', compact(
            'revenueByMonth',
            'bookingsByMonth',
            'topVehicles',
            'avgBookingValue',
            'from'
        ));
    }
}
