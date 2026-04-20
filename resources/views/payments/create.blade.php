@extends('layouts.app')

@section('title', 'Record Payment')

@section('content')
@php
    $defaultAmount = old('amount', $booking->payment?->amount ?? $booking->calculateTotalAmount());
@endphp
<div class="mx-auto max-w-3xl">
    <div class="mb-8">
        <h1 class="text-2xl font-semibold tracking-tight text-slate-50">Record payment</h1>
        <p class="mt-1 text-sm text-slate-400">Booking #{{ $booking->id }} — amount is usually (days × daily rate). Mark as paid when you receive cash or other settlement.</p>
    </div>

    <div class="rr-panel-elevated p-6 sm:p-8">
        <form method="POST" action="{{ route('payments.store', $booking) }}" class="space-y-5" id="payment-form">
            @csrf
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                <div>
                    <label class="rr-label" for="amount">Amount (₱)</label>
                    <input id="amount" type="number" step="0.01" name="amount" value="{{ $defaultAmount }}" required class="rr-input">
                </div>
                <div>
                    <label class="rr-label" for="payment_method">Payment method</label>
                    <select id="payment_method" name="payment_method" class="rr-input">
                        @php
                            $methods = ['cash' => 'Cash', 'gcash' => 'GCash', 'bank_transfer' => 'Bank transfer', 'card' => 'Card', 'other' => 'Other'];
                            $currentMethod = old('payment_method', $booking->payment && $booking->payment->payment_method !== 'pending' ? $booking->payment->payment_method : 'cash');
                        @endphp
                        @foreach($methods as $value => $label)
                            <option value="{{ $value }}" @selected($currentMethod === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="rr-label" for="payment_status">Status</label>
                    <select id="payment_status" name="payment_status" class="rr-input">
                        @foreach(['pending','paid','failed','refunded'] as $status)
                            <option value="{{ $status }}" @selected(old('payment_status', $booking->payment?->payment_status ?? 'pending') === $status)>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="max-w-xs">
                <label class="rr-label" for="payment_date">Payment date</label>
                <input id="payment_date" type="date" name="payment_date" value="{{ old('payment_date', optional($booking->payment?->payment_date)->format('Y-m-d') ?? now()->format('Y-m-d')) }}" class="rr-input">
            </div>
            <div class="flex flex-wrap gap-3 pt-2">
                <button type="submit" class="inline-flex items-center rounded-lg rr-btn-primary px-5 py-2.5 text-sm font-semibold shadow-sm">Save</button>
                <a href="{{ route('payments.index') }}" class="inline-flex items-center rounded-lg rr-btn-secondary px-5 py-2.5 text-sm font-semibold">Cancel</a>
            </div>
        </form>
    </div>
</div>
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('payment-form');
        const statusSelect = document.getElementById('payment_status');
        if (!form || !statusSelect || !window.Swal) return;

        form.addEventListener('submit', function (e) {
            if (form.dataset.confirmed === '1') return;

            e.preventDefault();
            const status = statusSelect.value;
            const isPaid = status === 'paid';

            Swal.fire({
                icon: isPaid ? 'question' : 'warning',
                title: isPaid ? 'Confirm payment as paid?' : 'Save payment record?',
                text: isPaid
                    ? 'Marking as paid will confirm the booking (if it was pending) and mark the vehicle as rented when applicable.'
                    : 'This will save the payment status for this booking.',
                showCancelButton: true,
                confirmButtonText: isPaid ? 'Yes, mark paid' : 'Yes, save payment',
                confirmButtonColor: isPaid ? '#10b981' : '#7c3aed',
            }).then((result) => {
                if (result.isConfirmed) {
                    form.dataset.confirmed = '1';
                    form.submit();
                }
            });
        });
    });
</script>
@endpush
@endsection
