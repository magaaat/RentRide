@extends('layouts.app')

@section('title', 'Super Admin Dashboard')

@section('content')
<div class="mb-4">
    <h3 class="text-xl font-semibold">Super Admin Dashboard</h3>
    <p class="text-sm text-slate-300">Platform overview and key metrics.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <div class="rounded-xl border border-slate-800 bg-slate-900/60 p-5">
        <div class="text-sm text-slate-300">Total Tenants</div>
        <div class="mt-2 text-3xl font-bold">{{ $totalTenants }}</div>
    </div>
    <div class="rounded-xl border border-slate-800 bg-slate-900/60 p-5">
        <div class="text-sm text-slate-300">Active Subscriptions</div>
        <div class="mt-2 text-3xl font-bold">{{ $activeSubscriptions }}</div>
    </div>
    <div class="rounded-xl border border-slate-800 bg-slate-900/60 p-5">
        <div class="text-sm text-slate-300">Platform Revenue</div>
        <div class="mt-2 text-3xl font-bold">₱{{ number_format($platformRevenue, 2) }}</div>
    </div>
</div>
@endsection
