@extends('layouts.app')

@section('title', 'Customer')

@section('content')
<div class="mb-4 flex items-center justify-between">
    <h3 class="text-xl font-semibold">{{ $customer->name }}</h3>
    <div class="flex gap-2">
        <a href="{{ route('customers.edit', $customer) }}" class="inline-flex items-center rounded-lg bg-slate-800 px-3 py-1.5 text-xs font-semibold text-slate-100 hover:bg-slate-700">
            Edit
        </a>
        <a href="{{ route('customers.index') }}" class="inline-flex items-center rounded-lg border border-slate-600 px-3 py-1.5 text-xs font-semibold text-slate-200 hover:bg-slate-800">
            Back to list
        </a>
    </div>
</div>

<div class="rounded-xl border rr-border rr-surface p-5 text-sm space-y-2">
    <p><span class="text-slate-400">Email:</span> {{ $customer->email ?? '—' }}</p>
    <p><span class="text-slate-400">Phone:</span> {{ $customer->phone ?? '—' }}</p>
    <p><span class="text-slate-400">Address:</span> {{ $customer->address ?? '—' }}</p>
</div>

@if($customer->bookings()->exists())
    <h4 class="mt-6 mb-2 font-semibold text-slate-200">Recent bookings</h4>
    <div class="overflow-hidden rounded-xl border rr-border rr-surface">
        <table class="min-w-full text-sm">
            <thead class="rr-table-head text-slate-200">
            <tr>
                <th class="px-4 py-2 text-left">Vehicle</th>
                <th class="px-4 py-2 text-left">Start</th>
                <th class="px-4 py-2 text-left">End</th>
                <th class="px-4 py-2 text-left">Status</th>
            </tr>
            </thead>
            <tbody class="divide-y" style="border-color: var(--rr-border);">
            @foreach($customer->bookings()->with('vehicle')->latest()->limit(10)->get() as $booking)
                <tr class="rr-row-hover">
                    <td class="px-4 py-2">{{ $booking->vehicle?->vehicle_name ?? '—' }}</td>
                    <td class="px-4 py-2">{{ $booking->start_date->format('Y-m-d') }}</td>
                    <td class="px-4 py-2">{{ $booking->end_date->format('Y-m-d') }}</td>
                    <td class="px-4 py-2 capitalize">{{ $booking->status }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endif
@endsection
