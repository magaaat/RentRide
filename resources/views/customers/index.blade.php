@extends('layouts.app')

@section('title', 'Customers')

@section('content')
<div class="flex items-center justify-between mb-4">
    <h3 class="text-xl font-semibold">Customers</h3>
    <a href="{{ route('customers.create') }}" class="inline-flex items-center rounded-lg rr-btn-primary px-3 py-1.5 text-xs font-semibold">
        Add customer
    </a>
</div>

<div class="overflow-hidden rounded-xl border rr-border rr-surface">
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
                    <td class="px-4 py-3 text-right space-x-2">
                        <a href="{{ route('customers.show', $customer) }}" class="inline-flex items-center rounded-lg border border-slate-600 px-3 py-1.5 text-xs font-semibold text-slate-200 hover:bg-slate-800">
                            View
                        </a>
                        <a href="{{ route('customers.edit', $customer) }}" class="inline-flex items-center rounded-lg bg-slate-800 px-3 py-1.5 text-xs font-semibold text-slate-100 hover:bg-slate-700">
                            Edit
                        </a>
                        <form action="{{ route('customers.destroy', $customer) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center rounded-lg bg-red-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-red-500">
                                Delete
                            </button>
                        </form>
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
