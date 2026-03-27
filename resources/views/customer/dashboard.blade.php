@extends('layouts.app')

@section('title', 'My rentals - RentRide')

@section('content')
<div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
    <div>
        <h1 class="text-2xl font-semibold">Welcome, {{ $user->name }}</h1>
        <p class="mt-1 text-sm text-slate-400">Search vehicles, pick a rental company, and track your bookings — all in one place.</p>
    </div>
</div>

<h2 class="text-lg font-semibold text-slate-200 mb-3">Rental companies</h2>
<p class="text-sm text-slate-400 mb-4">Choose a company to see available cars and prices.</p>
<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 mb-10">
    @forelse($tenants as $t)
        <a href="{{ route('customer.tenants.vehicles', $t) }}" class="group rounded-xl border border-slate-800 bg-slate-900/60 p-5 hover:border-emerald-500/40 hover:bg-slate-900 transition">
            <div class="font-semibold text-slate-100 group-hover:text-emerald-300">{{ $t->company_name }}</div>
            <p class="mt-2 text-sm text-slate-400 line-clamp-2">{{ $t->address ?? 'Address on file' }}</p>
            @if($t->phone)
                <p class="mt-1 text-xs text-slate-500">Phone: {{ $t->phone }}</p>
            @endif
            <span class="mt-3 inline-flex text-xs font-semibold text-emerald-400">View vehicles →</span>
        </a>
    @empty
        <p class="text-slate-400 text-sm col-span-full">No rental companies are available yet. Check back soon.</p>
    @endforelse
</div>

<h2 class="text-lg font-semibold text-slate-200 mb-3">Active bookings</h2>
<div class="overflow-x-auto rounded-xl border border-slate-800 mb-10">
    <table class="min-w-full text-sm">
        <thead class="bg-slate-800/80 text-slate-200">
            <tr class="text-left">
                <th class="px-4 py-3 font-semibold">Company</th>
                <th class="px-4 py-3 font-semibold">Vehicle</th>
                <th class="px-4 py-3 font-semibold">Dates</th>
                <th class="px-4 py-3 font-semibold">Booking</th>
                <th class="px-4 py-3 font-semibold">Payment</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-800">
            @forelse($activeBookings as $booking)
                <tr class="hover:bg-slate-800/40">
                    <td class="px-4 py-3 text-slate-300">{{ $booking->tenant->company_name }}</td>
                    <td class="px-4 py-3">{{ $booking->vehicle->vehicle_name ?? '—' }}</td>
                    <td class="px-4 py-3 text-slate-400">{{ $booking->start_date->format('M j, Y') }} – {{ $booking->end_date->format('M j, Y') }}</td>
                    <td class="px-4 py-3">
                        <span class="rounded-full bg-amber-500/15 px-2 py-0.5 text-xs font-medium text-amber-200">{{ ucfirst($booking->status) }}</span>
                    </td>
                    <td class="px-4 py-3 text-slate-400">
                        @if($booking->payment)
                            <span class="text-slate-200">{{ ucfirst($booking->payment->payment_status) }}</span>
                            @if($booking->payment->payment_status === 'paid')
                                <span class="block text-xs">₱{{ number_format($booking->payment->amount, 2) }}</span>
                            @endif
                        @else
                            <span class="text-slate-500">Pending</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-slate-500">No active bookings. Search for a vehicle to get started.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<h2 class="text-lg font-semibold text-slate-200 mb-3">Rental history</h2>
<div class="overflow-x-auto rounded-xl border border-slate-800">
    <table class="min-w-full text-sm">
        <thead class="bg-slate-800/80 text-slate-200">
            <tr class="text-left">
                <th class="px-4 py-3 font-semibold">Company</th>
                <th class="px-4 py-3 font-semibold">Vehicle</th>
                <th class="px-4 py-3 font-semibold">Dates</th>
                <th class="px-4 py-3 font-semibold">Status</th>
                <th class="px-4 py-3 font-semibold">Payment</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-800">
            @forelse($history as $booking)
                <tr class="hover:bg-slate-800/40">
                    <td class="px-4 py-3 text-slate-300">{{ $booking->tenant->company_name }}</td>
                    <td class="px-4 py-3">{{ $booking->vehicle->vehicle_name ?? '—' }}</td>
                    <td class="px-4 py-3 text-slate-400">{{ $booking->start_date->format('M j, Y') }} – {{ $booking->end_date->format('M j, Y') }}</td>
                    <td class="px-4 py-3">
                        <span class="rounded-full bg-slate-600/40 px-2 py-0.5 text-xs text-slate-200">{{ ucfirst($booking->status) }}</span>
                    </td>
                    <td class="px-4 py-3 text-slate-400">
                        {{ $booking->payment ? ucfirst($booking->payment->payment_status) : '—' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-slate-500">No past rentals yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
