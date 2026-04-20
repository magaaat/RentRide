@extends('layouts.app')

@section('title', 'Bookings')

@section('content')
@php
    $tenantUser = auth()->user();
    $tn = $tenantUser->tenant;
    $canRecordPayment = $tenantUser->hasPermission('payments.manage') && $tn?->hasFeature(\App\Models\Tenant::FEATURE_PAYMENT_TRACKING);
@endphp
<div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
    <div>
        <h1 class="text-2xl font-semibold tracking-tight text-slate-50 sm:text-3xl">Bookings</h1>
        <p class="mt-1 text-sm text-slate-400">Confirm reservations only after payment is received. Record cash or other methods under Payments.</p>
    </div>
    <div class="flex flex-wrap gap-2">
        @if(auth()->user()->tenant?->hasFeature(\App\Models\Tenant::FEATURE_BOOKING_CALENDAR))
            <a href="{{ route('bookings.calendar') }}" class="inline-flex items-center rounded-lg border border-slate-600 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:bg-slate-800">
                Calendar
            </a>
        @endif
        <a href="{{ route('bookings.create') }}" class="inline-flex items-center rounded-lg rr-btn-primary px-4 py-2 text-sm font-semibold shadow-sm">
            Create booking
        </a>
    </div>
</div>

<div class="overflow-hidden rounded-xl border rr-border rr-surface shadow-rr">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="rr-table-head text-slate-200">
            <tr>
                <th class="px-4 py-3 text-left font-semibold">Vehicle</th>
                <th class="px-4 py-3 text-left font-semibold">Customer</th>
                <th class="px-4 py-3 text-left font-semibold">Start</th>
                <th class="px-4 py-3 text-left font-semibold">End</th>
                <th class="px-4 py-3 text-left font-semibold">Total</th>
                <th class="px-4 py-3 text-left font-semibold">Payment</th>
                <th class="px-4 py-3 text-left font-semibold">Status</th>
                <th class="px-4 py-3 text-right font-semibold">Actions</th>
            </tr>
            </thead>
            <tbody class="divide-y" style="--tw-divide-opacity: 1; border-color: var(--rr-border);">
            @forelse($bookings as $booking)
                <tr class="rr-row-hover">
                    <td class="px-4 py-3">{{ $booking->vehicle->vehicle_name ?? '-' }}</td>
                    <td class="px-4 py-3">
                        <div class="text-slate-200">{{ $booking->customer->name ?? '—' }}</div>
                        @if($booking->customer)
                            <a href="{{ route('customers.show', $booking->customer) }}" class="rr-link-accent mt-1 inline-block text-xs font-semibold">View profile</a>
                        @endif
                    </td>
                    <td class="px-4 py-3">{{ $booking->start_date->format('Y-m-d') }}</td>
                    <td class="px-4 py-3">{{ $booking->end_date->format('Y-m-d') }}</td>
                    <td class="px-4 py-3">₱{{ number_format($booking->calculateTotalAmount(), 2) }}</td>
                    <td class="px-4 py-3">
                        @if($booking->payment)
                            <span class="capitalize">{{ $booking->payment->payment_status }}</span>
                            @if($booking->payment->payment_status === 'paid')
                                <span class="block text-xs text-slate-500">{{ $booking->payment->payment_method }}</span>
                            @endif
                        @else
                            <span class="text-slate-400">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 capitalize">{{ $booking->status }}</td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex flex-col items-end gap-2 sm:flex-row sm:justify-end sm:gap-2">
                            @if($canRecordPayment)
                                <a href="{{ route('payments.create', $booking) }}" class="inline-flex items-center rounded-lg border border-slate-600 bg-slate-50 px-3 py-1.5 text-xs font-semibold text-slate-800 hover:bg-slate-100">
                                    Record payment
                                </a>
                            @endif
                            <form
                                method="POST"
                                action="{{ route('bookings.updateStatus', $booking) }}"
                                class="inline-flex flex-wrap items-center justify-end gap-2"
                                data-confirm
                                data-confirm-icon="question"
                                data-confirm-color="#7c3aed"
                                data-confirm-title="Update booking status?"
                                data-confirm-text="Confirmed requires payment marked as paid first (unless already confirmed)."
                                data-confirm-button="Yes, update"
                            >
                                @csrf
                                <select name="status" class="rounded-lg border border-slate-700 bg-slate-950/40 px-2 py-1 text-xs text-slate-100 focus:outline-none focus:ring-2 focus:ring-violet-500">
                                    @foreach(['pending','confirmed','cancelled','completed'] as $status)
                                        @if($status === 'confirmed' && ! $booking->hasPaidPayment() && $booking->status !== 'confirmed')
                                            @continue
                                        @endif
                                        <option value="{{ $status }}" @selected($booking->status === $status)>{{ ucfirst($status) }}</option>
                                    @endforeach
                                </select>
                                <button class="rr-btn-primary inline-flex items-center rounded-lg px-3 py-1.5 text-xs font-semibold">
                                    Update
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-4 py-6 text-center text-slate-400">No bookings found.</td>
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
