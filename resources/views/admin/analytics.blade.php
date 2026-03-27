@extends('layouts.app')

@section('title', 'Advanced analytics')

@section('content')
<div class="mb-6">
    <h3 class="text-xl font-semibold">Advanced analytics</h3>
    <p class="text-sm text-slate-400">Premium — trends since {{ $from->format('M Y') }}.</p>
</div>

<div class="grid gap-4 md:grid-cols-2">
    <div class="rounded-xl border rr-border rr-surface p-5">
        <h4 class="font-semibold text-slate-200 mb-3">Revenue by month (paid)</h4>
        <ul class="space-y-2 text-sm">
            @forelse($revenueByMonth as $ym => $total)
                <li class="flex justify-between border-b border-slate-800/80 pb-2">
                    <span class="text-slate-400">{{ $ym }}</span>
                    <span class="font-semibold">₱{{ number_format((float) $total, 2) }}</span>
                </li>
            @empty
                <li class="text-slate-500">No payment data in this range.</li>
            @endforelse
        </ul>
    </div>
    <div class="rounded-xl border rr-border rr-surface p-5">
        <h4 class="font-semibold text-slate-200 mb-3">New bookings by month</h4>
        <ul class="space-y-2 text-sm">
            @forelse($bookingsByMonth as $ym => $count)
                <li class="flex justify-between border-b border-slate-800/80 pb-2">
                    <span class="text-slate-400">{{ $ym }}</span>
                    <span class="font-semibold">{{ $count }}</span>
                </li>
            @empty
                <li class="text-slate-500">No bookings in this range.</li>
            @endforelse
        </ul>
    </div>
</div>

<div class="mt-6 grid gap-4 md:grid-cols-2">
    <div class="rounded-xl border rr-border rr-surface p-5">
        <h4 class="font-semibold text-slate-200 mb-2">Average payment amount</h4>
        <div class="text-2xl font-bold">
            @if($avgBookingValue)
                ₱{{ number_format((float) $avgBookingValue, 2) }}
            @else
                <span class="text-slate-500 text-base">—</span>
            @endif
        </div>
    </div>
    <div class="rounded-xl border rr-border rr-surface p-5">
        <h4 class="font-semibold text-slate-200 mb-3">Top vehicles by booking count</h4>
        <ol class="list-decimal list-inside space-y-1 text-sm">
            @forelse($topVehicles as $row)
                <li>
                    {{ $row->vehicle?->vehicle_name ?? 'Vehicle #'.$row->vehicle_id }}
                    <span class="text-slate-400">({{ $row->booking_count }})</span>
                </li>
            @empty
                <li class="text-slate-500 list-none">No bookings yet.</li>
            @endforelse
        </ol>
    </div>
</div>
@endsection
