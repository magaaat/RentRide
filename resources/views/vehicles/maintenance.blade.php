@extends('layouts.app')

@section('title', 'Maintenance')

@section('content')
<div class="mb-4">
    <h3 class="text-xl font-semibold">Maintenance tracking</h3>
    <p class="text-sm text-slate-400">Track issues, priority, and expected repair dates for vehicles under maintenance.</p>
</div>

<div class="overflow-hidden rounded-xl border rr-border rr-surface">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="rr-table-head text-slate-200">
            <tr>
                <th class="px-4 py-3 text-left font-semibold">Vehicle details</th>
                <th class="px-4 py-3 text-left font-semibold">Issue</th>
                <th class="px-4 py-3 text-left font-semibold">Severity</th>
                <th class="px-4 py-3 text-left font-semibold">Reported</th>
                <th class="px-4 py-3 text-left font-semibold">Target fix</th>
                <th class="px-4 py-3 text-left font-semibold">Actual fix</th>
                <th class="px-4 py-3 text-right font-semibold">Est. cost</th>
            </tr>
            </thead>
            <tbody class="divide-y" style="border-color: var(--rr-border);">
            @forelse($vehicles as $vehicle)
                <tr class="rr-row-hover">
                    <td class="px-4 py-3">
                        <p class="font-semibold text-slate-900">{{ $vehicle->vehicle_name }}</p>
                        <p class="text-xs text-slate-500">{{ $vehicle->brand }} • {{ $vehicle->vehicle_type }}</p>
                        <p class="text-xs text-slate-500">Plate: {{ $vehicle->plate_number }}</p>
                    </td>
                    <td class="px-4 py-3">
                        {{ $vehicle->maintenance_issue ?: ($vehicle->description ?: 'No issue notes provided.') }}
                    </td>
                    <td class="px-4 py-3">
                        @php
                            $severity = $vehicle->maintenance_severity;
                            $severityClasses = match ($severity) {
                                'critical' => 'border-rose-300 bg-rose-100 text-rose-800',
                                'high' => 'border-amber-300 bg-amber-100 text-amber-800',
                                'medium' => 'border-yellow-300 bg-yellow-100 text-yellow-800',
                                'low' => 'border-emerald-300 bg-emerald-100 text-emerald-800',
                                default => 'border-slate-300 bg-slate-100 text-slate-700',
                            };
                        @endphp
                        <span class="inline-flex rounded-full border px-2 py-0.5 text-xs font-semibold uppercase tracking-wide {{ $severityClasses }}">
                            {{ $severity ?: 'Not set' }}
                        </span>
                    </td>
                    <td class="px-4 py-3">{{ $vehicle->maintenance_reported_at?->format('M d, Y') ?: $vehicle->updated_at?->format('M d, Y') }}</td>
                    <td class="px-4 py-3">{{ $vehicle->maintenance_target_fix_at?->format('M d, Y') ?: 'TBD' }}</td>
                    <td class="px-4 py-3">{{ $vehicle->maintenance_fixed_at?->format('M d, Y') ?: 'Not fixed yet' }}</td>
                    <td class="px-4 py-3 text-right">
                        {{ $vehicle->maintenance_cost_estimate !== null ? '₱' . number_format((float) $vehicle->maintenance_cost_estimate, 2) : '—' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-slate-500">
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
