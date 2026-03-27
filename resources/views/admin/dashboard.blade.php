@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="mb-4">
    <h3 class="text-xl font-semibold">Rental Admin Dashboard</h3>
    <p class="text-sm text-slate-300">Quick overview of your tenant activity.</p>
</div>

@if(($pendingBookingsCount ?? 0) > 0 || ($pendingExtensionCount ?? 0) > 0)
    <div class="mb-4 space-y-2">
        @if(($pendingBookingsCount ?? 0) > 0)
            <div class="flex items-center gap-2 rounded-xl border border-amber-500/30 bg-amber-500/10 px-4 py-3 text-sm text-amber-100">
                <span class="inline-block h-2 w-2 shrink-0 rounded-full bg-rose-500" aria-hidden="true"></span>
                <span>
                    You have <strong>{{ $pendingBookingsCount }}</strong> booking{{ $pendingBookingsCount === 1 ? '' : 's' }} awaiting confirmation.
                    <a href="{{ route('bookings.index') }}" class="underline font-semibold text-amber-200 hover:text-white">Review bookings</a>
                </span>
            </div>
        @endif
        @if(($pendingExtensionCount ?? 0) > 0)
            <div class="flex items-center gap-2 rounded-xl border border-sky-500/30 bg-sky-500/10 px-4 py-3 text-sm text-sky-100">
                <span class="inline-block h-2 w-2 shrink-0 rounded-full bg-rose-500" aria-hidden="true"></span>
                <span>
                    Your plan extension request is <strong>pending</strong> review. You will regain access once it is approved.
                </span>
            </div>
        @endif
    </div>
@endif

<div class="mb-4 rounded-xl border border-slate-800 bg-slate-900/50 p-4 text-sm text-slate-300">
    <span class="font-semibold text-slate-200">Plan:</span> {{ ucfirst($plan ?? 'basic') }}
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

<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <div class="rounded-xl border border-slate-800 bg-slate-900/60 p-5">
        <div class="text-sm text-slate-300">Total Vehicles</div>
        <div class="mt-2 text-3xl font-bold">{{ $totalVehicles }}</div>
    </div>
    <div class="rounded-xl border border-slate-800 bg-slate-900/60 p-5">
        <div class="text-sm text-slate-300">Active Bookings</div>
        <div class="mt-2 text-3xl font-bold">{{ $activeBookings }}</div>
    </div>
    <div class="rounded-xl border border-slate-800 bg-slate-900/60 p-5">
        <div class="text-sm text-slate-300">Revenue</div>
        <div class="mt-2 text-3xl font-bold">₱{{ number_format($revenue, 2) }}</div>
    </div>
</div>
@endsection

