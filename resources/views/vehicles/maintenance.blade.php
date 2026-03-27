@extends('layouts.app')

@section('title', 'Maintenance')

@section('content')
<div class="mb-4">
    <h3 class="text-xl font-semibold">Maintenance tracking</h3>
    <p class="text-sm text-slate-400">Vehicles marked as <span class="text-amber-200">maintenance</span> (Premium).</p>
</div>

<div class="overflow-hidden rounded-xl border rr-border rr-surface">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="rr-table-head text-slate-200">
            <tr>
                <th class="px-4 py-3 text-left font-semibold">Vehicle</th>
                <th class="px-4 py-3 text-left font-semibold">Type</th>
                <th class="px-4 py-3 text-left font-semibold">Plate</th>
                <th class="px-4 py-3 text-right font-semibold">Daily rate</th>
            </tr>
            </thead>
            <tbody class="divide-y" style="border-color: var(--rr-border);">
            @forelse($vehicles as $vehicle)
                <tr class="rr-row-hover">
                    <td class="px-4 py-3">{{ $vehicle->vehicle_name }}</td>
                    <td class="px-4 py-3">{{ $vehicle->vehicle_type }}</td>
                    <td class="px-4 py-3">{{ $vehicle->plate_number }}</td>
                    <td class="px-4 py-3 text-right">₱{{ number_format($vehicle->price_per_day, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-4 py-8 text-center text-slate-500">
                        No vehicles in maintenance. Set a vehicle’s status to “maintenance” on the vehicle edit screen.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($vehicles->hasPages())
    <div class="mt-4">{{ $vehicles->links() }}</div>
@endif
@endsection
