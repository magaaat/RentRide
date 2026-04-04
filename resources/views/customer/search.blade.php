@extends('layouts.app')

@section('title', 'Search vehicles - RentRide')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-semibold tracking-tight text-slate-50 sm:text-3xl">Search all rental vehicles</h1>
    <p class="mt-2 max-w-2xl text-sm leading-relaxed text-slate-400">Filter by type, price, location, and dates. Results include vehicles from approved rental companies.</p>
</div>

<form method="GET" class="rr-panel-elevated mb-8 p-5 sm:p-6">
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <div>
            <label class="rr-label-sm" for="vehicle_type">Vehicle type</label>
            <input id="vehicle_type" type="text" name="vehicle_type" value="{{ request('vehicle_type') }}" placeholder="e.g. Sedan" class="rr-input">
        </div>
        <div>
            <label class="rr-label-sm" for="location">Location / company</label>
            <input id="location" type="text" name="location" value="{{ request('location') }}" placeholder="City or company name" class="rr-input">
        </div>
        <div class="flex items-end pb-0.5">
            <label class="inline-flex cursor-pointer items-center gap-2.5 text-sm text-slate-300">
                <input type="checkbox" name="bookable_only" value="1" @checked(request('bookable_only')) class="size-4 rounded border-slate-600 bg-slate-900 text-violet-500 focus:ring-2 focus:ring-violet-500/50">
                <span>Only show available vehicles</span>
            </label>
        </div>
    </div>
    <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div>
            <label class="rr-label-sm" for="min_price">Min ₱ / day</label>
            <input id="min_price" type="number" name="min_price" value="{{ request('min_price') }}" step="0.01" min="0" class="rr-input">
        </div>
        <div>
            <label class="rr-label-sm" for="max_price">Max ₱ / day</label>
            <input id="max_price" type="number" name="max_price" value="{{ request('max_price') }}" step="0.01" min="0" class="rr-input">
        </div>
        <div>
            <label class="rr-label-sm" for="start_date">Pick-up</label>
            <input id="start_date" type="date" name="start_date" value="{{ request('start_date') }}" class="rr-input">
        </div>
        <div>
            <label class="rr-label-sm" for="end_date">Return</label>
            <input id="end_date" type="date" name="end_date" value="{{ request('end_date') }}" class="rr-input">
        </div>
    </div>
    <div class="mt-6 flex flex-wrap items-center gap-3">
        <button type="submit" class="rr-btn-primary inline-flex h-10 items-center justify-center rounded-lg px-6 text-sm font-semibold shadow-sm transition">
            Search
        </button>
        <a href="{{ route('customer.search') }}" class="text-sm font-medium text-slate-400 underline-offset-2 hover:text-slate-200 hover:underline">Clear filters</a>
    </div>
</form>

<div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
    @forelse($vehicles as $vehicle)
        @php $t = $vehicle->tenant; @endphp
        <article class="group flex flex-col overflow-hidden rounded-xl border border-slate-700/80 bg-slate-900/50 shadow-rr-sm transition duration-200 hover:border-slate-600 hover:bg-slate-900/70 hover:shadow-rr">
            <div class="relative aspect-video bg-slate-800">
                @if($vehicle->image)
                    <img src="{{ asset('storage/'.$vehicle->image) }}" alt="" class="h-full w-full object-cover transition duration-300 group-hover:scale-[1.02]">
                @else
                    <div class="flex h-full items-center justify-center text-xs text-slate-500">No photo</div>
                @endif
            </div>
            <div class="flex flex-1 flex-col p-5">
                <div class="text-xs font-semibold uppercase tracking-wide text-violet-300/95">{{ $t->company_name }}</div>
                <h2 class="mt-1.5 text-lg font-semibold leading-snug text-slate-50">{{ $vehicle->vehicle_name }}</h2>
                <p class="mt-1 text-xs text-slate-500">{{ $vehicle->vehicle_type }} · {{ $vehicle->brand }}</p>
                <p class="mt-3 text-lg font-bold text-violet-300">₱{{ number_format($vehicle->price_per_day, 2) }}<span class="text-sm font-normal text-slate-500">/day</span></p>
                <div class="mt-auto w-full shrink-0 pt-5">
                    <a href="{{ route('customer.vehicles.show', [$t, $vehicle]) }}" class="rr-btn-primary flex h-11 w-full items-center justify-center rounded-lg text-sm font-semibold leading-none no-underline shadow-sm transition">
                        View & book
                    </a>
                </div>
            </div>
        </article>
    @empty
        <div class="col-span-full rounded-xl border border-dashed border-slate-700 bg-slate-900/30 px-6 py-14 text-center">
            <p class="text-slate-400">No vehicles match your filters.</p>
            <p class="mt-1 text-sm text-slate-500">Try clearing filters or widening the price range.</p>
        </div>
    @endforelse
</div>

<div class="mt-10">{{ $vehicles->links() }}</div>
@endsection
