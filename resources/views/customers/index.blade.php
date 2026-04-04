@extends('layouts.app')

@section('title', 'Customers')

@section('content')
<div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
    <div>
        <h1 class="text-2xl font-semibold tracking-tight text-slate-50 sm:text-3xl">Customers</h1>
        <p class="mt-1 text-sm text-slate-400">People who have booked with your company. Profile details are managed by customers in their RentRide account.</p>
    </div>
    <a href="{{ route('customers.create') }}" class="inline-flex items-center justify-center rounded-lg rr-btn-primary px-4 py-2.5 text-sm font-semibold shadow-sm">
        Add customer
    </a>
</div>

<div class="overflow-hidden rounded-xl border rr-border rr-surface shadow-rr">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="rr-table-head text-slate-200">
            <tr>
                <th class="px-4 py-3 text-left font-semibold">Name</th>
                <th class="px-4 py-3 text-left font-semibold">Email</th>
                <th class="px-4 py-3 text-left font-semibold">Phone</th>
                <th class="px-4 py-3 text-left font-semibold">Address</th>
                <th class="px-4 py-3 text-right font-semibold">Actions</th>
            </tr>
            </thead>
            <tbody class="divide-y" style="border-color: var(--rr-border);">
            @forelse($customers as $customer)
                <tr class="rr-row-hover">
                    <td class="px-4 py-3">{{ $customer->name }}</td>
                    <td class="px-4 py-3">{{ $customer->email ?? '—' }}</td>
                    <td class="px-4 py-3">{{ $customer->phone ?? '—' }}</td>
                    <td class="px-4 py-3 max-w-xs truncate" title="{{ $customer->address }}">{{ $customer->address ?? '—' }}</td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex flex-wrap items-center justify-end gap-2">
                            <a href="{{ route('customers.show', $customer) }}" class="inline-flex items-center rounded-lg border border-slate-600 px-3 py-1.5 text-xs font-semibold text-slate-200 transition hover:bg-slate-800">
                                View
                            </a>
                            <form
                                action="{{ route('customers.destroy', $customer) }}"
                                method="POST"
                                class="inline"
                                data-confirm
                                data-confirm-title="Delete customer?"
                                data-confirm-text="This customer record will be removed."
                                data-confirm-button="Yes, delete customer"
                            >
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center rounded-lg bg-red-600/90 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-red-500">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-6 text-center text-slate-400">No customers yet.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    {{ $customers->links() }}
</div>
@endsection
