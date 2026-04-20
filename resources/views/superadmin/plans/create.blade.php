@extends('layouts.app')

@section('title', 'Create plan')

@section('content')
<div class="flex items-center justify-between mb-4">
    <div>
        <h3 class="text-xl font-semibold">Create plan</h3>
        <p class="text-sm text-slate-400">New subscription plan</p>
    </div>
    <a href="{{ route('superadmin.plans.index') }}" class="inline-flex items-center rounded-lg border border-slate-700 px-3 py-1.5 text-xs font-semibold text-slate-200 hover:bg-slate-800">Back</a>
</div>

<div class="rounded-xl border border-slate-800 bg-slate-900/60 p-5">
    <form method="POST" action="{{ route('superadmin.plans.store') }}" class="space-y-4">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Key <span class="text-slate-500">(slug, unique)</span></label>
                <input type="text" name="key" value="{{ old('key') }}" required pattern="[a-z0-9_\-]+" title="Lowercase letters, numbers, hyphen, underscore"
                       class="w-full rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-violet-500" placeholder="e.g. premium_2026">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Display name</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       class="w-full rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-violet-500">
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Tier label</label>
                <input type="text" name="tier" value="{{ old('tier') }}" required maxlength="64"
                       class="w-full rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-violet-500" placeholder="e.g. Promo, Launch offer">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Feature tier</label>
                <select name="feature_tier" class="w-full rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-violet-500">
                    @foreach(['basic' => 'Basic', 'standard' => 'Standard', 'premium' => 'Premium'] as $k => $label)
                        <option value="{{ $k }}" @selected(old('feature_tier', 'basic') === $k)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Sort order</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', 40) }}" min="0" max="65535"
                       class="w-full rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-violet-500">
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Base price (₱)</label>
                <input type="number" step="0.01" min="0" name="base_price" value="{{ old('base_price', '0') }}" required
                       class="w-full rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-violet-500">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Billing period</label>
                <input type="text" name="billing_period" value="{{ old('billing_period', 'month') }}" required
                       class="w-full rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-violet-500">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Currency</label>
                <input type="text" name="currency" value="{{ old('currency', 'PHP') }}" maxlength="3" required
                       class="w-full rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-violet-500">
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Discount type</label>
                <select name="discount_type" class="w-full rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-violet-500">
                    @foreach(['none' => 'None', 'percent' => 'Percent', 'fixed' => 'Fixed'] as $k => $label)
                        <option value="{{ $k }}" @selected(old('discount_type', 'none') === $k)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Discount value</label>
                <input type="number" step="0.01" min="0" name="discount_value" value="{{ old('discount_value', '0') }}"
                       class="w-full rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-violet-500">
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Features (one per line)</label>
            <textarea name="features_text" rows="5" class="w-full rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-violet-500">{{ old('features_text') }}</textarea>
        </div>
        <div class="space-y-3 rounded-lg border border-slate-800 bg-slate-950/30 p-4">
            <div class="flex items-center gap-2">
                <input type="hidden" name="show_on_landing" value="0">
                <input type="checkbox" id="show_on_landing" name="show_on_landing" value="1" @checked(old('show_on_landing', true)) class="size-4 rounded border-slate-600 bg-slate-900 text-violet-500">
                <label for="show_on_landing" class="text-sm font-medium">Show</label>
            </div>
            <div class="flex items-center gap-2">
                <input type="hidden" name="enable_plan" value="0">
                <input type="checkbox" id="enable_plan" name="enable_plan" value="1" @checked(old('enable_plan', true)) class="size-4 rounded border-slate-600 bg-slate-900 text-violet-500">
                <label for="enable_plan" class="text-sm font-medium">Enable</label>
            </div>
        </div>
        <button type="submit" class="rr-btn-primary inline-flex items-center rounded-lg px-4 py-2 text-sm font-semibold">Create plan</button>
    </form>
</div>
@endsection
