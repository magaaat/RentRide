@extends('layouts.app')

@section('title', 'Rental companies - RentRide')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-semibold">Browse rental companies</h1>
    <p class="mt-1 text-sm text-slate-400">Filter by city or name, then open a company to see vehicles.</p>
</div>

<form method="GET" class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end">
    <div class="flex-1">
        <label class="block text-xs font-medium text-slate-400 mb-1">Location / keyword</label>
        <input type="text" name="location" value="{{ request('location') }}" placeholder="City, address, or company name"
            class="w-full rounded-lg border border-slate-600 bg-slate-900/70 px-3 py-2 text-sm">
    </div>
    <button type="submit" class="rr-btn-primary rounded-lg px-5 py-2 text-sm font-semibold">Search</button>
</form>

<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
    @forelse($tenants as $t)
        <a href="{{ route('customer.tenants.vehicles', $t) }}" class="rounded-xl border border-slate-800 bg-slate-900/60 p-5 hover:border-violet-500/40 transition">
            <div class="font-semibold text-slate-100">{{ $t->company_name }}</div>
            <p class="mt-2 text-sm text-slate-400">{{ $t->address ?? '—' }}</p>
            @if($t->phone)
                <p class="mt-2 text-xs text-slate-500">{{ $t->phone }}</p>
            @endif
            <span class="mt-3 inline-block text-xs font-semibold text-violet-300">View cars</span>
        </a>
    @empty
        <p class="text-slate-500 col-span-full">No companies match your search.</p>
    @endforelse
</div>

<div class="mt-8">{{ $tenants->links() }}</div>
@endsection
