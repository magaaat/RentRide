@extends('layouts.app')

@section('title', 'Super Admin Dashboard')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-semibold tracking-tight text-slate-50 sm:text-3xl">Super Admin dashboard</h1>
    <p class="mt-2 text-sm text-slate-400">Platform overview and key metrics.</p>
</div>

<div class="grid grid-cols-1 gap-5 md:grid-cols-3">
    <div class="rr-panel-elevated p-6">
        <div class="text-sm font-medium text-slate-400">Total tenants</div>
        <div class="mt-2 text-3xl font-bold tabular-nums tracking-tight text-slate-50">{{ $totalTenants }}</div>
    </div>
    <div class="rr-panel-elevated p-6">
        <div class="text-sm font-medium text-slate-400">Active subscriptions</div>
        <div class="mt-2 text-3xl font-bold tabular-nums tracking-tight text-slate-50">{{ $activeSubscriptions }}</div>
    </div>
    <div class="rr-panel-elevated p-6">
        <div class="text-sm font-medium text-slate-400">Platform revenue</div>
        <div class="mt-2 text-3xl font-bold tabular-nums tracking-tight text-violet-300">₱{{ number_format($platformRevenue, 2) }}</div>
    </div>
</div>
@endsection
