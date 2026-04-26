@extends('layouts.app')

@section('title', 'Domain Disabled - RentRide')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center py-10">
    <div class="w-full max-w-4xl rr-surface border rr-border rounded-3xl shadow-2xl p-8 backdrop-blur">
        <div class="text-center">
            <div class="inline-flex items-center rounded-full border rr-border rr-surface-2 px-3 py-1 text-xs font-semibold text-slate-200">
                Tenant: {{ $tenant->company_name }}
            </div>
            <h2 class="mt-4 text-2xl font-semibold">Access unavailable</h2>
            @if(($isMaintenanceMode ?? false) === true)
                <p class="mt-2 text-sm text-slate-300">This tenant is currently in maintenance mode. Please contact Super Admin.</p>
            @else
                <p class="mt-2 text-sm text-slate-300">Subscription expired. Request an extension below.</p>
            @endif
        </div>

        @if(($isMaintenanceMode ?? false) === true)
            <div class="mt-6 rounded-xl border rr-border rr-surface-2 p-4 text-sm text-slate-200">
                Domain is manually disabled for maintenance.
            </div>
        @elseif($hasPending)
            <div class="mt-6 rounded-xl border rr-border rr-surface-2 p-4 text-sm text-slate-200">
                Extension request pending.
            </div>
        @else
            <div class="mt-8">
                <h3 class="text-sm font-semibold text-slate-200 mb-3 text-center">Request extension</h3>

                <form method="POST" action="{{ route('tenant.extend.request') }}" id="extend-form">
                    @csrf
                    <input type="hidden" name="tenant_id" value="{{ $tenant->id }}">

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @forelse($plans as $plan)
                            @php
                                $final = $plan->discountedPrice();
                                $base = (float) $plan->base_price;
                                $hasDiscount = $plan->discount_type !== 'none' && (float)$plan->discount_value > 0;
                                $discountLabel = $hasDiscount
                                    ? ($plan->discount_type === 'percent'
                                        ? number_format((float)$plan->discount_value, 0) . '% OFF'
                                        : '₱' . number_format((float)$plan->discount_value, 0) . ' OFF')
                                    : null;
                            @endphp
                            <label class="{{ $plan->is_active ? 'cursor-pointer' : 'cursor-not-allowed' }}">
                                <input type="radio" name="requested_plan" value="{{ $plan->key }}" class="hidden peer" @disabled(! $plan->is_active) required>
                                <div class="h-full rounded-2xl border rr-border rr-surface-2 p-5 {{ $plan->is_active ? 'peer-checked:ring-2 peer-checked:ring-offset-0 peer-checked:ring-[var(--rr-accent)]' : 'opacity-70' }}">
                                    <div class="flex items-center justify-between">
                                        <div class="text-sm font-semibold">{{ $plan->name }}</div>
                                        @if(! $plan->is_active)
                                            <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full border border-rose-500/30 bg-rose-500/10 text-rose-300">
                                                Disabled
                                            </span>
                                        @elseif($hasDiscount)
                                            <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full rr-surface border rr-border">
                                                {{ $discountLabel }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="mt-4 flex items-baseline gap-2">
                                        <div class="text-3xl font-extrabold">₱{{ number_format($final, 0) }}</div>
                                        @if($hasDiscount)
                                            <div class="text-sm text-slate-400 line-through">₱{{ number_format($base, 0) }}</div>
                                        @endif
                                    </div>
                                    <div class="mt-1 text-xs text-slate-400">per {{ $plan->billing_period }}</div>
                                    <ul class="mt-4 space-y-1 text-xs text-slate-200">
                                        @foreach(($plan->features ?? []) as $f)
                                            <li>- {{ $f }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </label>
                        @empty
                            <div class="md:col-span-3 rounded-2xl border rr-border rr-surface-2 p-5 text-sm text-slate-300">
                                No plans are available for extension at this time.
                            </div>
                        @endforelse
                    </div>

                    @if($plans->where('is_active', true)->isNotEmpty())
                        <div class="mt-6 flex items-center justify-center">
                            <button type="button" id="extend-button" class="inline-flex items-center rounded-lg rr-btn-primary px-5 py-2.5 text-sm font-semibold">
                                Request extension
                            </button>
                        </div>
                    @else
                        <div class="mt-6 text-center text-sm text-slate-300">No enabled plans available.</div>
                    @endif
                </form>
            </div>
        @endif
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const btn = document.getElementById('extend-button');
            const form = document.getElementById('extend-form');
            if (!btn || !form) return;

            btn.addEventListener('click', function () {
                const selected = form.querySelector('input[name="requested_plan"]:checked');
                if (!selected) {
                    if (window.Swal) {
                        Swal.fire({ icon: 'info', title: 'Select a plan', text: 'Please choose a plan to request an extension.' });
                    }
                    return;
                }

                if (window.Swal) {
                    Swal.fire({
                        icon: 'question',
                        title: 'Confirm extension request?',
                        text: 'Your request will be sent to the Super Admin for approval.',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, request',
                        confirmButtonColor: 'var(--rr-accent)',
                    }).then((r) => {
                        if (r.isConfirmed) form.submit();
                    });
                } else {
                    form.submit();
                }
            });
        });
    </script>
@endpush
@endsection

