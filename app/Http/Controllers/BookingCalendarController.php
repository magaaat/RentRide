<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BookingCalendarController extends TenantControllerBase
{
    public function index(Request $request)
    {
        $tenantId = $this->tenantId();

        $year = max(2000, min(2100, (int) $request->input('year', now()->year)));
        $month = max(1, min(12, (int) $request->input('month', now()->month)));

        $start = Carbon::create($year, $month, 1)->startOfDay();
        $end = $start->copy()->endOfMonth()->endOfDay();

        $bookings = Booking::with(['vehicle', 'customer'])
            ->where('tenant_id', $tenantId)
            ->where('start_date', '<=', $end->toDateString())
            ->where('end_date', '>=', $start->toDateString())
            ->orderBy('start_date')
            ->get();

        // Monday = 1 … Sunday = 7 (for padding before day 1)
        $firstWeekday = (int) $start->copy()->startOfMonth()->format('N');
        $daysInMonth = (int) $start->daysInMonth;

        $prev = $start->copy()->subMonth();
        $next = $start->copy()->addMonth();

        return view('bookings.calendar', compact(
            'bookings',
            'start',
            'end',
            'year',
            'month',
            'firstWeekday',
            'daysInMonth',
            'prev',
            'next'
        ));
    }
}
