@extends('layouts.app')

@section('title', 'Bookings')

@section('content')
<div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between mb-4">
    <h3 class="text-xl font-semibold">Bookings</h3>
    <div class="flex flex-wrap gap-2">
        @if(auth()->user()->tenant?->hasFeature(\App\Models\Tenant::FEATURE_BOOKING_CALENDAR))
            <a href="{{ route('bookings.calendar') }}" class="inline-flex items-center rounded-lg border border-slate-600 px-3 py-1.5 text-xs font-semibold text-slate-200 hover:bg-slate-800">
                Calendar
            </a>
        @endif
        <a href="{{ route('bookings.create') }}" class="inline-flex items-center rounded-lg rr-btn-primary px-3 py-1.5 text-xs font-semibold">
            Create Booking
        </a>
    </div>
</div>

<div class="overflow-hidden rounded-xl border rr-border rr-surface">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="rr-table-head text-slate-200">
            <tr>
                <th class="px-4 py-3 text-left font-semibold">Vehicle</th>
                <th class="px-4 py-3 text-left font-semibold">Customer</th>
                <th class="px-4 py-3 text-left font-semibold">Start</th>
                <th class="px-4 py-3 text-left font-semibold">End</th>
                <th class="px-4 py-3 text-left font-semibold">Status</th>
                <th class="px-4 py-3 text-right font-semibold">Update Status</th>
            </tr>
            </thead>
            <tbody class="divide-y" style="--tw-divide-opacity: 1; border-color: var(--rr-border);">
            @forelse($bookings as $booking)
                <tr class="rr-row-hover">
                    <td class="px-4 py-3">{{ $booking->vehicle->vehicle_name ?? '-' }}</td>
                    <td class="px-4 py-3">{{ $booking->customer->name ?? '-' }}</td>
                    <td class="px-4 py-3">{{ $booking->start_date->format('Y-m-d') }}</td>
                    <td class="px-4 py-3">{{ $booking->end_date->format('Y-m-d') }}</td>
                    <td class="px-4 py-3 capitalize">{{ $booking->status }}</td>
                    <td class="px-4 py-3 text-right">
                        <form method="POST" action="{{ route('bookings.updateStatus', $booking) }}" class="inline-flex items-center gap-2">
                            @csrf
                            <select name="status" class="rounded-lg border border-slate-700 bg-slate-950/40 px-2 py-1 text-xs text-slate-100 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                @foreach(['pending','confirmed','cancelled','completed'] as $status)
                                    <option value="{{ $status }}" @selected($booking->status === $status)>{{ ucfirst($status) }}</option>
                                @endforeach
                            </select>
                            <button class="inline-flex items-center rounded-lg bg-slate-800 px-3 py-1.5 text-xs font-semibold text-slate-100 hover:bg-slate-700">
                                Update
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-6 text-center text-slate-400">No bookings found.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    {{ $bookings->links() }}
</div>
@endsection

