@extends('layouts.app')

@section('title', 'Payments')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-semibold tracking-tight text-slate-50 sm:text-3xl">Payments</h1>
    <p class="mt-1 text-sm text-slate-400">Recorded payments for your bookings.</p>
</div>

<div class="overflow-hidden rounded-xl border rr-border rr-surface shadow-rr">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="rr-table-head text-slate-200">
            <tr>
                <th class="px-4 py-3 text-left font-semibold">Booking ID</th>
                <th class="px-4 py-3 text-left font-semibold">Amount</th>
                <th class="px-4 py-3 text-left font-semibold">Method</th>
                <th class="px-4 py-3 text-left font-semibold">Status</th>
                <th class="px-4 py-3 text-left font-semibold">Date</th>
            </tr>
            </thead>
            <tbody class="divide-y" style="--tw-divide-opacity: 1; border-color: var(--rr-border);">
            @forelse($payments as $payment)
                <tr class="rr-row-hover">
                    <td class="px-4 py-3">#{{ $payment->booking_id }}</td>
                    <td class="px-4 py-3">₱{{ number_format($payment->amount, 2) }}</td>
                    <td class="px-4 py-3">{{ $payment->payment_method }}</td>
                    <td class="px-4 py-3 capitalize">{{ $payment->payment_status }}</td>
                    <td class="px-4 py-3">{{ optional($payment->payment_date)->format('Y-m-d') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-6 text-center text-slate-400">No payments recorded.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    {{ $payments->links() }}
</div>
@endsection

