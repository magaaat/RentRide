@extends('layouts.app')

@section('title', $tenant->company_name . ' — Vehicles')

@section('content')
<div class="mb-6">
    <a href="{{ route('customer.tenants.index') }}" class="text-sm text-emerald-400 hover:text-emerald-300">← Back to companies</a>
    <h1 class="mt-2 text-2xl font-semibold">{{ $tenant->company_name }}</h1>
    <p class="mt-1 text-sm text-slate-400">{{ $tenant->address ?? '' }} @if($tenant->phone) · {{ $tenant->phone }} @endif</p>
</div>

<form method="GET" class="mb-6 rounded-xl border border-slate-800 bg-slate-900/40 p-4 space-y-3">
    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
        <div>
            <label class="block text-xs text-slate-400 mb-1">Vehicle type</label>
            <input type="text" name="vehicle_type" value="{{ request('vehicle_type') }}" list="types" placeholder="e.g. SUV"
                class="w-full rounded-lg border border-slate-600 bg-slate-900/70 px-3 py-2 text-sm">
            <datalist id="types">
                @foreach($vehicleTypes as $vt)
                    <option value="{{ $vt }}">
                @endforeach
            </datalist>
        </div>
        <div>
            <label class="block text-xs text-slate-400 mb-1">Min ₱ / day</label>
            <input type="number" name="min_price" value="{{ request('min_price') }}" step="0.01" min="0"
                class="w-full rounded-lg border border-slate-600 bg-slate-900/70 px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs text-slate-400 mb-1">Max ₱ / day</label>
            <input type="number" name="max_price" value="{{ request('max_price') }}" step="0.01" min="0"
                class="w-full rounded-lg border border-slate-600 bg-slate-900/70 px-3 py-2 text-sm">
        </div>
        <div class="flex items-end gap-2">
            <label class="inline-flex items-center gap-2 text-sm text-slate-300">
                <input type="checkbox" name="available_only" value="1" @checked(request('available_only'))>
                Available only
            </label>
        </div>
    </div>
    <div class="grid gap-3 sm:grid-cols-2">
        <div>
            <label class="block text-xs text-slate-400 mb-1">Pick-up date</label>
            <input type="date" name="start_date" value="{{ request('start_date') }}"
                class="w-full rounded-lg border border-slate-600 bg-slate-900/70 px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs text-slate-400 mb-1">Return date</label>
            <input type="date" name="end_date" value="{{ request('end_date') }}"
                class="w-full rounded-lg border border-slate-600 bg-slate-900/70 px-3 py-2 text-sm">
        </div>
    </div>
    <div class="flex gap-2">
        <button type="submit" class="rounded-lg bg-emerald-500 px-4 py-2 text-sm font-semibold text-slate-950 hover:bg-emerald-400">Apply filters</button>
        <a href="{{ route('customer.tenants.vehicles', $tenant) }}" class="rounded-lg border border-slate-600 px-4 py-2 text-sm text-slate-300 hover:bg-slate-800">Reset</a>
    </div>
</form>

<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
    @forelse($vehicles as $vehicle)
        <div class="rounded-xl border border-slate-800 bg-slate-900/60 overflow-hidden flex flex-col">
            <div class="aspect-video bg-slate-800 flex items-center justify-center text-slate-500 text-sm">
                @if($vehicle->image)
                    <img src="{{ asset('storage/'.$vehicle->image) }}" alt="" class="w-full h-full object-cover">
                @else
                    No photo
                @endif
            </div>
            <div class="p-4 flex-1 flex flex-col">
                <div class="font-semibold text-slate-100">{{ $vehicle->vehicle_name }}</div>
                <p class="text-xs text-slate-500 mt-1">{{ $vehicle->brand }} · {{ $vehicle->vehicle_type }}</p>
                <p class="mt-2 text-lg font-bold text-emerald-400">₱{{ number_format($vehicle->price_per_day, 2) }}<span class="text-sm font-normal text-slate-400">/day</span></p>
                <span class="mt-2 inline-flex w-fit rounded-full px-2 py-0.5 text-xs font-medium
                    {{ $vehicle->status === 'available' ? 'bg-emerald-500/20 text-emerald-200' : 'bg-slate-600/40 text-slate-300' }}">
                    {{ ucfirst($vehicle->status) }}
                </span>
                <a href="{{ route('customer.vehicles.show', [$tenant, $vehicle]) }}" class="mt-4 inline-flex justify-center rounded-lg bg-slate-700 py-2 text-sm font-semibold text-white hover:bg-slate-600">
                    Details & book
                </a>
            </div>
        </div>
    @empty
        <p class="text-slate-500 col-span-full">No vehicles match your filters.</p>
    @endforelse
</div>

<div class="mt-8">{{ $vehicles->links() }}</div>
@endsection
