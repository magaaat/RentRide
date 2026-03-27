@extends('layouts.app')

@section('title', 'Edit Plan')

@section('content')
<div class="flex items-center justify-between mb-4">
    <div>
        <h3 class="text-xl font-semibold">Edit Plan</h3>
        <p class="text-sm text-slate-300">{{ $plan->name }} ({{ $plan->key }})</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('superadmin.plans.index') }}" class="inline-flex items-center rounded-lg border border-slate-700 px-3 py-1.5 text-xs font-semibold text-slate-200 hover:bg-slate-800">
            Back to Plans
        </a>
    </div>
</div>

<div class="rounded-xl border border-slate-800 bg-slate-900/60 p-5">
    <form method="POST" action="{{ route('superadmin.plans.update', $plan) }}" class="space-y-4">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Base Price (₱)</label>
                <input type="number" step="0.01" min="0" name="base_price" value="{{ old('base_price', $plan->base_price) }}"
                       class="w-full rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Billing Period</label>
                <input type="text" value="{{ $plan->billing_period }}" disabled
                       class="w-full rounded-lg border border-slate-800 bg-slate-950/30 px-3 py-2 text-sm text-slate-400">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Discount Type</label>
                <select name="discount_type" class="w-full rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    @foreach(['none' => 'None', 'percent' => 'Percent (%)', 'fixed' => 'Fixed (₱)'] as $k => $label)
                        <option value="{{ $k }}" @selected(old('discount_type', $plan->discount_type) === $k)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Discount Value</label>
                <input type="number" step="0.01" min="0" name="discount_value" value="{{ old('discount_value', $plan->discount_value) }}"
                       class="w-full rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <p class="mt-1 text-xs text-slate-400">If type is “None”, value is ignored.</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <input type="checkbox" id="is_active" name="is_active" value="1" @checked(old('is_active', $plan->is_active))>
            <label for="is_active" class="text-sm font-medium">Active</label>
        </div>

        <div class="rounded-lg border border-slate-800 bg-slate-950/30 p-4 text-sm">
            <div class="font-semibold mb-1">Preview</div>
            <div class="text-slate-300">Final price after discount: <span class="font-semibold text-slate-100">₱{{ number_format($plan->discountedPrice(), 2) }}</span></div>
        </div>

        <div class="flex items-center justify-end gap-2 pt-2">
            <button class="inline-flex items-center rounded-lg bg-emerald-500 px-4 py-2 text-sm font-semibold text-slate-950 hover:bg-emerald-400">
                Save Changes
            </button>
        </div>
    </form>
</div>
@endsection

