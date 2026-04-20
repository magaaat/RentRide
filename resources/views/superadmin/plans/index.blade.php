@extends('layouts.app')

@section('title', 'Plans')

@section('content')
<div class="flex items-center justify-between mb-4">
    <div>
        <h3 class="text-xl font-semibold">Plans</h3>
        <p class="text-sm text-slate-400">Subscription plans and pricing</p>
    </div>
    <a href="{{ route('superadmin.plans.create') }}" class="rr-btn-primary inline-flex items-center rounded-lg px-4 py-2 text-sm font-semibold">
        Create plan
    </a>
</div>

@if(session('success'))
    <div class="mb-4 rounded-lg border border-emerald-500/30 bg-emerald-500/10 px-4 py-2 text-sm text-emerald-200">{{ session('success') }}</div>
@endif
@if($errors->has('delete'))
    <div class="mb-4 rounded-lg border border-red-500/40 bg-red-500/10 px-4 py-2 text-sm text-red-200">{{ $errors->first('delete') }}</div>
@endif

<div class="overflow-hidden rounded-xl border border-slate-800 bg-slate-900/60">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-800/80 text-slate-200">
            <tr>
                <th class="px-4 py-3 text-left font-semibold">Plan</th>
                <th class="px-4 py-3 text-left font-semibold">Tier / feature</th>
                <th class="px-4 py-3 text-left font-semibold">Base Price</th>
                <th class="px-4 py-3 text-left font-semibold">Discount</th>
                <th class="px-4 py-3 text-left font-semibold">Final Price</th>
                <th class="px-4 py-3 text-left font-semibold">Features</th>
                <th class="px-4 py-3 text-left font-semibold">Show</th>
                <th class="px-4 py-3 text-left font-semibold">Enable</th>
                <th class="px-4 py-3 text-right font-semibold">Actions</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-slate-800">
            @foreach($plans as $plan)
                <tr class="hover:bg-slate-800/60">
                    <td class="px-4 py-3 font-semibold">{{ $plan->name }} <span class="text-xs text-slate-400">({{ $plan->key }})</span></td>
                    <td class="px-4 py-3 text-slate-300">
                        <span class="font-medium">{{ $plan->tier }}</span>
                        <span class="text-xs text-slate-500"> / {{ $plan->feature_tier ?? $plan->tier }}</span>
                    </td>
                    <td class="px-4 py-3">₱{{ number_format((float)$plan->base_price, 2) }} / {{ $plan->billing_period }}</td>
                    <td class="px-4 py-3">
                        @if($plan->discount_type === 'none' || (float)$plan->discount_value <= 0)
                            <span class="text-slate-400">—</span>
                        @elseif($plan->discount_type === 'percent')
                            {{ number_format((float)$plan->discount_value, 0) }}%
                        @else
                            ₱{{ number_format((float)$plan->discount_value, 2) }}
                        @endif
                    </td>
                    <td class="px-4 py-3">₱{{ number_format($plan->discountedPrice(), 2) }}</td>
                    <td class="px-4 py-3">
                        <ul class="space-y-1 text-slate-200">
                            @foreach(($plan->features ?? []) as $f)
                                <li class="text-xs">- {{ $f }}</li>
                            @endforeach
                        </ul>
                    </td>
                    <td class="px-4 py-3">
                        @if($plan->show_on_landing ?? true)
                            <span class="rr-chip-accent inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold">Shown</span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-slate-500/20 px-2.5 py-0.5 text-xs font-semibold text-slate-300">Hidden</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        @if($plan->is_active)
                            <span class="rr-chip-accent inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold">Yes</span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-slate-500/20 px-2.5 py-0.5 text-xs font-semibold text-slate-300">No</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right whitespace-nowrap">
                        <a href="{{ route('superadmin.plans.edit', $plan) }}" class="inline-flex items-center rounded-lg bg-slate-800 px-3 py-1.5 text-xs font-semibold text-slate-100 hover:bg-slate-700">
                            Edit
                        </a>
                        <form method="POST" action="{{ route('superadmin.plans.destroy', $plan) }}" class="delete-plan-form ms-2 inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center rounded-lg border border-red-500/50 bg-red-500/10 px-3 py-1.5 text-xs font-semibold text-red-200 hover:bg-red-500/20">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.delete-plan-form').forEach(function (form) {
            form.addEventListener('submit', function (e) {
                if (!window.Swal) return;
                e.preventDefault();
                Swal.fire({
                    title: 'Delete plan?',
                    text: 'This cannot be undone.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, delete',
                }).then(function (r) { if (r.isConfirmed) form.submit(); });
            });
        });
    });
</script>
@endpush
