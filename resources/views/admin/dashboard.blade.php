@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-semibold tracking-tight text-slate-50 sm:text-3xl">Rental admin dashboard</h1>
    <p class="mt-2 text-sm text-slate-400">Quick overview of your tenant activity.</p>
</div>

@if(($pendingBookingsCount ?? 0) > 0 || ($pendingExtensionCount ?? 0) > 0)
    <div class="mb-6 space-y-3">
        @if(($pendingBookingsCount ?? 0) > 0)
            <div class="flex items-center gap-3 rounded-xl border border-amber-500/35 bg-amber-500/10 px-4 py-3.5 text-sm text-amber-100 shadow-rr-sm">
                <span class="inline-block h-2 w-2 shrink-0 rounded-full bg-rose-500" aria-hidden="true"></span>
                <span>
                    You have <strong>{{ $pendingBookingsCount }}</strong> booking{{ $pendingBookingsCount === 1 ? '' : 's' }} awaiting confirmation.
                    <a href="{{ route('bookings.index') }}" class="font-semibold text-amber-200 underline underline-offset-2 hover:text-white">Review bookings</a>
                </span>
            </div>
        @endif
        @if(($pendingExtensionCount ?? 0) > 0)
            <div class="flex items-center gap-3 rounded-xl border border-sky-500/35 bg-sky-500/10 px-4 py-3.5 text-sm text-sky-100 shadow-rr-sm">
                <span class="inline-block h-2 w-2 shrink-0 rounded-full bg-rose-500" aria-hidden="true"></span>
                <span>
                    Your plan extension request is <strong>pending</strong> review. You will regain access once it is approved.
                </span>
            </div>
        @endif
    </div>
@endif

<div class="rr-panel mb-8 px-5 py-4 text-sm text-slate-300">
    <span class="font-semibold text-slate-100">Plan:</span> {{ ucfirst($plan ?? 'basic') }}
    @if(isset($monthlyBookingLimit))
        <span class="mx-2 text-slate-600">|</span>
        Bookings this month (new): <strong class="text-slate-100">{{ $monthlyBookingsUsed ?? 0 }}</strong>
        @if($monthlyBookingLimit !== null)
            / {{ $monthlyBookingLimit }} (Basic cap)
        @endif
    @endif
    @if(isset($vehicleLimit) && $vehicleLimit !== null)
        <span class="mx-2 text-slate-600">|</span>
        Vehicle slots: <strong class="text-slate-100">{{ $totalVehicles }}</strong> / {{ $vehicleLimit }}
    @endif
</div>

<div class="grid grid-cols-1 gap-5 md:grid-cols-3">
    <div class="rr-panel-elevated p-6 transition hover:border-slate-600">
        <div class="text-sm font-medium text-slate-400">Total vehicles</div>
        <div class="mt-2 text-3xl font-bold tabular-nums tracking-tight text-slate-50">{{ $totalVehicles }}</div>
    </div>
    <div class="rr-panel-elevated p-6 transition hover:border-slate-600">
        <div class="text-sm font-medium text-slate-400">Active bookings</div>
        <div class="mt-2 text-3xl font-bold tabular-nums tracking-tight text-slate-50">{{ $activeBookings }}</div>
    </div>
    <div class="rr-panel-elevated p-6 transition hover:border-slate-600">
        <div class="text-sm font-medium text-slate-400">Revenue</div>
        <div class="mt-2 text-3xl font-bold tabular-nums tracking-tight text-violet-300">₱{{ number_format($revenue, 2) }}</div>
    </div>
</div>
@endsection
