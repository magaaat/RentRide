@extends('layouts.app')

@section('title', $tenant->company_name . ' — Vehicles')

@section('content')
<div class="mb-8">
    <a href="{{ route('customer.tenants.index') }}" class="text-sm font-medium text-violet-300 transition hover:text-violet-200">← Back to companies</a>
    <h1 class="mt-3 text-2xl font-semibold tracking-tight text-slate-50 sm:text-3xl">{{ $tenant->company_name }}</h1>
    <p class="mt-2 text-sm text-slate-400">{{ $tenant->address ?? '' }} @if($tenant->phone) · {{ $tenant->phone }} @endif</p>
</div>

<form method="GET" class="rr-panel-elevated mb-8 p-5 sm:p-6">
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div>
            <label class="rr-label-sm" for="vehicle_type">Vehicle type</label>
            <input id="vehicle_type" type="text" name="vehicle_type" value="{{ request('vehicle_type') }}" list="types" placeholder="e.g. SUV" class="rr-input">
            <datalist id="types">
                @foreach($vehicleTypes as $vt)
                    <option value="{{ $vt }}">
                @endforeach
            </datalist>
        </div>
        <div>
            <label class="rr-label-sm" for="min_price">Min ₱ / day</label>
            <input id="min_price" type="number" name="min_price" value="{{ request('min_price') }}" step="0.01" min="0" class="rr-input">
        </div>
        <div>
            <label class="rr-label-sm" for="max_price">Max ₱ / day</label>
            <input id="max_price" type="number" name="max_price" value="{{ request('max_price') }}" step="0.01" min="0" class="rr-input">
        </div>
        <div class="flex items-end pb-0.5">
            <label class="inline-flex cursor-pointer items-center gap-2.5 text-sm text-slate-300">
                <input type="checkbox" name="available_only" value="1" @checked(request('available_only')) class="size-4 rounded border-slate-600 bg-slate-900 text-violet-500 focus:ring-violet-500/50">
                Available only
            </label>
        </div>
    </div>
    <div class="mt-4 grid gap-4 sm:grid-cols-2">
        <div>
            <label class="rr-label-sm" for="start_date">Pick-up date</label>
            <input id="start_date" type="date" name="start_date" value="{{ request('start_date') }}" class="rr-input">
        </div>
        <div>
            <label class="rr-label-sm" for="end_date">Return date</label>
            <input id="end_date" type="date" name="end_date" value="{{ request('end_date') }}" class="rr-input">
        </div>
    </div>
    <div class="mt-6 flex flex-wrap items-center gap-3">
        <button type="submit" class="rr-btn-primary inline-flex h-10 items-center justify-center rounded-lg px-5 text-sm font-semibold shadow-sm transition">Apply filters</button>
        <a href="{{ route('customer.tenants.vehicles', $tenant) }}" class="text-sm font-medium text-slate-400 underline-offset-2 hover:text-slate-200 hover:underline">Reset</a>
    </div>
</form>

<div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
    @forelse($vehicles as $vehicle)
        <article class="group flex flex-col overflow-hidden rounded-xl border border-slate-700/80 bg-slate-900/50 shadow-rr-sm transition hover:border-slate-600 hover:shadow-rr">
            <div class="relative aspect-video bg-slate-800">
                @if($vehicle->image)
                    <img src="{{ asset('storage/'.$vehicle->image) }}" alt="" class="h-full w-full object-cover transition duration-300 group-hover:scale-[1.02]">
                @else
                    <div class="flex h-full items-center justify-center text-xs text-slate-500">No photo</div>
                @endif
            </div>
            <div class="flex flex-1 flex-col p-5">
                <h2 class="text-lg font-semibold text-slate-50">{{ $vehicle->vehicle_name }}</h2>
                <p class="mt-1 text-xs text-slate-500">{{ $vehicle->brand }} · {{ $vehicle->vehicle_type }}</p>
                <p class="mt-3 text-lg font-bold text-violet-300">₱{{ number_format($vehicle->price_per_day, 2) }}<span class="text-sm font-normal text-slate-500">/day</span></p>
                <span class="mt-2 inline-flex w-fit rounded-full px-2.5 py-0.5 text-xs font-medium
                    {{ $vehicle->status === 'available' ? 'bg-violet-500/20 text-violet-200' : 'bg-slate-600/40 text-slate-300' }}">
                    {{ ucfirst($vehicle->status) }}
                </span>
                <div class="mt-4 w-full shrink-0">
                    <a href="{{ route('customer.vehicles.show', [$tenant, $vehicle]) }}" class="rr-btn-primary flex h-11 w-full items-center justify-center rounded-lg text-sm font-semibold leading-none no-underline shadow-sm transition">
                        Details & book
                    </a>
                </div>
            </div>
        </article>
    @empty
        <div class="col-span-full rounded-xl border border-dashed border-slate-700 bg-slate-900/30 px-6 py-12 text-center text-slate-400">
            No vehicles match your filters.
        </div>
    @endforelse
</div>

<div class="mt-10">{{ $vehicles->links() }}</div>
@endsection
