@extends('layouts.app')

@section('title', 'Rental reports')

@section('content')
<div class="mb-6">
    <h3 class="text-xl font-semibold">Rental reports</h3>
    <p class="text-sm text-slate-400">Overview for your fleet and bookings (all plans).</p>
</div>

<div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
    <div class="rounded-xl border rr-border rr-surface p-5">
        <div class="text-sm text-slate-400">Total bookings</div>
        <div class="mt-1 text-2xl font-bold">{{ number_format($bookingsTotal) }}</div>
    </div>
    <div class="rounded-xl border rr-border rr-surface p-5">
        <div class="text-sm text-slate-400">Paid revenue</div>
        <div class="mt-1 text-2xl font-bold">₱{{ number_format($revenueTotal, 2) }}</div>
    </div>
    <div class="rounded-xl border rr-border rr-surface p-5">
        <div class="text-sm text-slate-400">Vehicles</div>
        <div class="mt-1 text-2xl font-bold">
            {{ $vehiclesCount }}
            @if($vehicleCap !== null)
                <span class="text-sm font-normal text-slate-400">/ {{ $vehicleCap }} max</span>
            @else
                <span class="text-sm font-normal text-slate-400">(unlimited)</span>
            @endif
        </div>
    </div>
</div>

<div class="mt-6 rounded-xl border rr-border rr-surface p-5">
    <h4 class="font-semibold text-slate-200">Plan usage ({{ ucfirst($plan) }})</h4>
    <p class="mt-2 text-sm text-slate-400">
        New bookings created this calendar month:
        <strong class="text-slate-200">{{ $monthlyUsed }}</strong>
        @if($monthlyLimit !== null)
            / {{ $monthlyLimit }} (Basic plan monthly cap)
        @else
            — unlimited on your plan
        @endif
    </p>
    <p class="mt-2 text-sm text-slate-400">
        Bookings added this month: <strong class="text-slate-200">{{ $bookingsThisMonth }}</strong>
    </p>
</div>

<div class="mt-6 rounded-xl border rr-border rr-surface p-5">
    <h4 class="font-semibold text-slate-200 mb-3">Bookings by status</h4>
    <div class="flex flex-wrap gap-3 text-sm">
        @forelse($bookingsByStatus as $status => $count)
            <span class="rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-1.5 capitalize">
                {{ $status }}: <strong>{{ $count }}</strong>
            </span>
        @empty
            <span class="text-slate-500">No data yet.</span>
        @endforelse
    </div>
</div>

@if(auth()->user()->tenant?->hasFeature(\App\Models\Tenant::FEATURE_ADVANCED_ANALYTICS))
    <div class="mt-6">
        <a href="{{ route('tenant.analytics') }}" class="inline-flex items-center rounded-lg rr-btn-primary px-4 py-2 text-sm font-semibold">
            Open advanced analytics (Premium)
        </a>
    </div>
@endif
@endsection
