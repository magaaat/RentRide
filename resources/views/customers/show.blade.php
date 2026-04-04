@extends('layouts.app')

@section('title', $customer->name . ' — Customer')

@section('content')
<div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
    <div>
        <h1 class="text-2xl font-semibold tracking-tight text-slate-50 sm:text-3xl">{{ $customer->name }}</h1>
        <p class="mt-2 max-w-2xl text-sm leading-relaxed text-slate-400">Read-only snapshot from bookings. Customers update name, contact info, and license in their <strong class="font-medium text-slate-300">RentRide profile</strong> — not here.</p>
    </div>
    <a href="{{ route('customers.index') }}" class="inline-flex shrink-0 items-center rounded-lg border border-slate-600 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:bg-slate-800">
        ← Back to customers
    </a>
</div>

<div class="rr-panel-elevated p-5 sm:p-6">
    <h2 class="text-xs font-semibold uppercase tracking-wide text-slate-500">On-file record</h2>
    <dl class="mt-4 grid gap-3 text-sm sm:grid-cols-2">
        <div>
            <dt class="text-slate-500">Email</dt>
            <dd class="mt-0.5 font-medium text-slate-100">{{ $customer->email ?? '—' }}</dd>
        </div>
        <div>
            <dt class="text-slate-500">Phone</dt>
            <dd class="mt-0.5 font-medium text-slate-100">{{ $customer->phone ?? '—' }}</dd>
        </div>
        <div class="sm:col-span-2">
            <dt class="text-slate-500">Address</dt>
            <dd class="mt-0.5 text-slate-200">{{ $customer->address ?? '—' }}</dd>
        </div>
    </dl>
</div>

@if($portalUser)
    <div class="rr-panel-elevated mt-6 p-5 sm:p-6">
        <h2 class="text-lg font-semibold text-slate-100">Portal account</h2>
        <p class="mt-1 text-sm text-slate-400">Same email as this record — live data from the customer’s RentRide login.</p>
        <dl class="mt-5 grid gap-3 text-sm sm:grid-cols-2">
            <div>
                <dt class="text-slate-500">Name</dt>
                <dd class="mt-0.5 font-medium text-slate-100">{{ $portalUser->name }}</dd>
            </div>
            <div>
                <dt class="text-slate-500">Email</dt>
                <dd class="mt-0.5 font-medium text-slate-100">{{ $portalUser->email }}</dd>
            </div>
            @if($portalUser->phone)
                <div>
                    <dt class="text-slate-500">Phone</dt>
                    <dd class="mt-0.5 text-slate-200">{{ $portalUser->phone }}</dd>
                </div>
            @endif
            @if($portalUser->address)
                <div class="sm:col-span-2">
                    <dt class="text-slate-500">Address</dt>
                    <dd class="mt-0.5 text-slate-200">{{ $portalUser->address }}</dd>
                </div>
            @endif
        </dl>
        @if($portalUser->hasDriverLicensePhotos())
            <p class="mt-6 text-sm font-semibold text-slate-200">Driver’s license</p>
            <div class="mt-3 grid gap-4 sm:grid-cols-2">
                <div>
                    <p class="mb-2 text-xs text-slate-500">Front</p>
                    <a href="{{ asset('storage/'.$portalUser->driver_license_front_path) }}" target="_blank" rel="noopener noreferrer" class="block overflow-hidden rounded-lg border border-slate-700 bg-slate-950/50">
                        <img src="{{ asset('storage/'.$portalUser->driver_license_front_path) }}" alt="License front" class="max-h-56 w-full object-contain">
                    </a>
                </div>
                <div>
                    <p class="mb-2 text-xs text-slate-500">Back</p>
                    <a href="{{ asset('storage/'.$portalUser->driver_license_back_path) }}" target="_blank" rel="noopener noreferrer" class="block overflow-hidden rounded-lg border border-slate-700 bg-slate-950/50">
                        <img src="{{ asset('storage/'.$portalUser->driver_license_back_path) }}" alt="License back" class="max-h-56 w-full object-contain">
                    </a>
                </div>
            </div>
        @else
            <p class="mt-6 rounded-lg border border-amber-500/25 bg-amber-500/5 px-4 py-3 text-sm text-amber-100/95">No driver’s license photos uploaded in the portal yet.</p>
        @endif
    </div>
@elseif($customer->email)
    <div class="mt-6 rounded-xl border border-slate-700/80 bg-slate-900/40 px-4 py-4 text-sm text-slate-400">
        No RentRide portal account matches this email — license images and portal details are unavailable.
    </div>
@endif

@if($customer->bookings()->exists())
    <h2 class="mb-3 mt-10 text-lg font-semibold text-slate-100">Recent bookings</h2>
    <div class="overflow-hidden rounded-xl border rr-border rr-surface shadow-rr-sm">
        <table class="min-w-full text-sm">
            <thead class="rr-table-head text-slate-200">
            <tr>
                <th class="px-4 py-3 text-left font-semibold">Vehicle</th>
                <th class="px-4 py-3 text-left font-semibold">Start</th>
                <th class="px-4 py-3 text-left font-semibold">End</th>
                <th class="px-4 py-3 text-left font-semibold">Status</th>
            </tr>
            </thead>
            <tbody class="divide-y" style="border-color: var(--rr-border);">
            @foreach($customer->bookings()->with('vehicle')->latest()->limit(10)->get() as $booking)
                <tr class="rr-row-hover">
                    <td class="px-4 py-3">{{ $booking->vehicle?->vehicle_name ?? '—' }}</td>
                    <td class="px-4 py-3 tabular-nums text-slate-300">{{ $booking->start_date->format('Y-m-d') }}</td>
                    <td class="px-4 py-3 tabular-nums text-slate-300">{{ $booking->end_date->format('Y-m-d') }}</td>
                    <td class="px-4 py-3 capitalize">{{ $booking->status }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endif
@endsection
