@extends('layouts.app')

@section('title', 'Register Tenant - RentRide')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center py-8">
    <div class="w-full max-w-3xl bg-slate-800/80 border border-slate-700 rounded-2xl shadow-2xl p-8 backdrop-blur">
        <h3 class="text-2xl font-semibold text-center mb-6">Register Rental Company</h3>
        <form method="POST" action="{{ route('tenant.register.post') }}" class="space-y-5">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Company Name</label>
                    <input type="text" name="company_name" value="{{ old('company_name') }}" required
                           class="w-full rounded-lg border border-slate-600 bg-slate-900/70 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Owner Name</label>
                    <input type="text" name="owner_name" value="{{ old('owner_name') }}" required
                           class="w-full rounded-lg border border-slate-600 bg-slate-900/70 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-transparent">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                       class="w-full rounded-lg border border-slate-600 bg-slate-900/70 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-transparent">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" required
                           class="w-full rounded-lg border border-slate-600 bg-slate-900/70 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Address</label>
                    <input type="text" name="address" value="{{ old('address') }}"
                           class="w-full rounded-lg border border-slate-600 bg-slate-900/70 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-transparent">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Password</label>
                    <input type="password" name="password" required
                           class="w-full rounded-lg border border-slate-600 bg-slate-900/70 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Confirm Password</label>
                    <input type="password" name="password_confirmation" required
                           class="w-full rounded-lg border border-slate-600 bg-slate-900/70 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-transparent">
                </div>
            </div>

            <div>
                @php
                    $selectedPlan = old('plan', request('plan', 'basic'));
                    $plan = \App\Models\SubscriptionPlan::where('key', $selectedPlan)->first();
                    $basePrice = (float) ($plan?->base_price ?? 0);
                    $finalPrice = $plan ? $plan->discountedPrice() : $basePrice;
                    $hasDiscount = $plan && $plan->discount_type !== 'none' && (float) $plan->discount_value > 0;
                    $discountLabel = null;
                    if ($hasDiscount) {
                        $discountLabel = $plan->discount_type === 'percent'
                            ? number_format((float) $plan->discount_value, 0) . '% OFF'
                            : '₱' . number_format((float) $plan->discount_value, 0) . ' OFF';
                    }
                @endphp
                <label class="block text-sm font-medium mb-2">Selected Plan</label>
                <input type="hidden" name="plan" value="{{ $selectedPlan }}">
                <div class="rounded-xl border border-violet-500/60 bg-slate-900/60 px-4 py-3 text-sm flex items-center justify-between">
                    <div>
                        <div class="text-xs uppercase tracking-wide text-violet-300/80">Plan</div>
                        <div class="font-semibold">
                            {{ $plan?->name ?? ucfirst($selectedPlan) }}
                            <span class="text-slate-300 font-normal">
                                (₱{{ number_format($finalPrice, 0) }} / month
                                @if($hasDiscount)
                                    <span class="line-through text-slate-400 ms-1">₱{{ number_format($basePrice, 0) }}</span>
                                @endif
                                )
                            </span>
                        </div>
                    </div>
                    <span class="rr-chip-accent inline-flex items-center rounded-full px-3 py-1 text-[11px] font-semibold">
                        @if($hasDiscount)
                            {{ $discountLabel }}
                        @else
                            Locked from previous selection
                        @endif
                    </span>
                </div>
            </div>

            <div class="flex items-center justify-between pt-2">
                <a href="{{ url('/') }}" class="text-sm text-slate-300 hover:text-violet-300 transition">Back to home</a>
                <button class="rr-btn-primary inline-flex justify-center items-center rounded-lg px-6 py-2.5 text-sm font-semibold shadow-lg transition">
                    Register
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

