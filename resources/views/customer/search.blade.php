@extends('layouts.app')

@section('title', 'Search vehicles - RentRide')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-semibold">Search all rental vehicles</h1>
    <p class="mt-1 text-sm text-slate-400">Filter by type, price, location, and date availability across approved rental companies.</p>
</div>

<form method="GET" class="mb-6 rounded-xl border border-slate-800 bg-slate-900/40 p-4 space-y-3">
    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
        <div>
            <label class="block text-xs text-slate-400 mb-1">Vehicle type</label>
            <input type="text" name="vehicle_type" value="{{ request('vehicle_type') }}" placeholder="e.g. Sedan"
                class="w-full rounded-lg border border-slate-600 bg-slate-900/70 px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs text-slate-400 mb-1">Location / company</label>
            <input type="text" name="location" value="{{ request('location') }}"
                class="w-full rounded-lg border border-slate-600 bg-slate-900/70 px-3 py-2 text-sm">
        </div>
        <div class="flex items-end">
            <label class="inline-flex items-center gap-2 text-sm text-slate-300">
                <input type="checkbox" name="bookable_only" value="1" @checked(request('bookable_only'))>
                Show only bookable (available status)
            </label>
        </div>
    </div>
    <div class="grid gap-3 sm:grid-cols-4">
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
        <div>
            <label class="block text-xs text-slate-400 mb-1">Pick-up</label>
            <input type="date" name="start_date" value="{{ request('start_date') }}"
                class="w-full rounded-lg border border-slate-600 bg-slate-900/70 px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs text-slate-400 mb-1">Return</label>
            <input type="date" name="end_date" value="{{ request('end_date') }}"
                class="w-full rounded-lg border border-slate-600 bg-slate-900/70 px-3 py-2 text-sm">
        </div>
    </div>
    <button type="submit" class="rounded-lg bg-emerald-500 px-4 py-2 text-sm font-semibold text-slate-950 hover:bg-emerald-400">Search</button>
</form>

<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
    @forelse($vehicles as $vehicle)
        @php $t = $vehicle->tenant; @endphp
        <div class="rounded-xl border border-slate-800 bg-slate-900/60 overflow-hidden flex flex-col">
            <div class="aspect-video bg-slate-800 flex items-center justify-center text-slate-500 text-xs">
                @if($vehicle->image)
                    <img src="{{ asset('storage/'.$vehicle->image) }}" alt="" class="w-full h-full object-cover">
                @else
                    No photo
                @endif
            </div>
            <div class="p-4 flex-1 flex flex-col">
                <div class="text-xs text-emerald-400 font-semibold">{{ $t->company_name }}</div>
                <div class="font-semibold text-slate-100 mt-1">{{ $vehicle->vehicle_name }}</div>
                <p class="text-xs text-slate-500">{{ $vehicle->vehicle_type }} · {{ $vehicle->brand }}</p>
                <p class="mt-2 font-bold text-emerald-400">₱{{ number_format($vehicle->price_per_day, 2) }}<span class="text-slate-400 font-normal text-sm">/day</span></p>
                <a href="{{ route('customer.vehicles.show', [$t, $vehicle]) }}" class="mt-auto pt-4 inline-flex justify-center rounded-lg bg-slate-700 py-2 text-sm font-semibold text-white hover:bg-slate-600">
                    View & book
                </a>
            </div>
        </div>
    @empty
        <p class="text-slate-500 col-span-full">No vehicles found. Try widening your search.</p>
    @endforelse
</div>

<div class="mt-8">{{ $vehicles->links() }}</div>
@endsection
