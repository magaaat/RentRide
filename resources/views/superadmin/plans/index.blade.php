@extends('layouts.app')

@section('title', 'Plans')

@section('content')
<div class="flex items-center justify-between mb-4">
    <div>
        <h3 class="text-xl font-semibold">Plans</h3>
        <p class="text-sm text-slate-300">Manage subscription pricing, discounts, and availability.</p>
    </div>
</div>

<div class="overflow-hidden rounded-xl border border-slate-800 bg-slate-900/60">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-800/80 text-slate-200">
            <tr>
                <th class="px-4 py-3 text-left font-semibold">Plan</th>
                <th class="px-4 py-3 text-left font-semibold">Base Price</th>
                <th class="px-4 py-3 text-left font-semibold">Discount</th>
                <th class="px-4 py-3 text-left font-semibold">Final Price</th>
                <th class="px-4 py-3 text-left font-semibold">Features</th>
                <th class="px-4 py-3 text-left font-semibold">Status</th>
                <th class="px-4 py-3 text-right font-semibold">Actions</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-slate-800">
            @foreach($plans as $plan)
                <tr class="hover:bg-slate-800/60">
                    <td class="px-4 py-3 font-semibold">{{ $plan->name }} <span class="text-xs text-slate-400">({{ $plan->key }})</span></td>
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
                        @if($plan->is_active)
                            <span class="inline-flex items-center rounded-full bg-emerald-500/10 px-2.5 py-0.5 text-xs font-semibold text-emerald-300">Active</span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-slate-500/20 px-2.5 py-0.5 text-xs font-semibold text-slate-300">Disabled</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('superadmin.plans.edit', $plan) }}" class="inline-flex items-center rounded-lg bg-slate-800 px-3 py-1.5 text-xs font-semibold text-slate-100 hover:bg-slate-700">
                            Edit
                        </a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

