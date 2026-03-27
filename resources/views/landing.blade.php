@extends('layouts.app')

@section('title', 'RentRide - Multi-tenant Rental Platform')

@section('content')
<div class="relative overflow-hidden rounded-3xl border border-slate-800 bg-gradient-to-br from-slate-950 via-indigo-950/40 to-slate-950 px-6 py-14 sm:px-10">
    <div class="pointer-events-none absolute inset-0 opacity-30" style="background-image: radial-gradient(circle at 20% 20%, rgba(99,102,241,0.45), transparent 45%), radial-gradient(circle at 80% 30%, rgba(16,185,129,0.35), transparent 40%), radial-gradient(circle at 55% 85%, rgba(168,85,247,0.35), transparent 45%);"></div>
    <div class="relative max-w-3xl">
        <p class="inline-flex items-center rounded-full border border-emerald-500/30 bg-emerald-500/10 px-3 py-1 text-xs font-semibold text-emerald-200">
            Multi-tenant car rental management system
        </p>
        <h1 class="mt-5 text-3xl font-bold tracking-tight sm:text-5xl">
            Run your rental business faster with a platform built for tenants.
        </h1>
        <p class="mt-4 text-slate-300 sm:text-lg">
        RentRide helps car rental businesses manage vehicles, bookings, customers, and payments in one powerful platform while giving you full control over your operations and growth.
        </p>

        <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:flex-wrap">
            <a href="#pricing" class="inline-flex items-center justify-center rounded-xl bg-emerald-500 px-5 py-3 text-sm font-semibold text-slate-950 hover:bg-emerald-400">
                View subscription plans
            </a>
            <a href="{{ route('customer.login') }}" class="inline-flex items-center justify-center rounded-xl bg-sky-500 px-5 py-3 text-sm font-semibold text-slate-950 hover:bg-sky-400">
                Rent a car
            </a>
            <a href="{{ route('superadmin.login') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-700 bg-slate-900/40 px-5 py-3 text-sm font-semibold text-slate-100 hover:bg-slate-800/50">
                Super Admin Login
            </a>
        </div>

        <div class="mt-10 grid gap-4 sm:grid-cols-3">
    <div class="rounded-2xl border border-slate-800 bg-slate-950/40 p-4">
        <div class="text-sm font-semibold">Easy Booking Management</div>
        <div class="mt-1 text-sm text-slate-300">
            Manage reservations, schedules, and customer bookings in one simple dashboard.
        </div>
    </div>

    <div class="rounded-2xl border border-slate-800 bg-slate-950/40 p-4">
        <div class="text-sm font-semibold">Vehicle & Fleet Tracking</div>
        <div class="mt-1 text-sm text-slate-300">
            Monitor vehicle availability, status, and maintenance with real-time updates.
        </div>
    </div>

    <div class="rounded-2xl border border-slate-800 bg-slate-950/40 p-4">
        <div class="text-sm font-semibold">Automated Payments & Reports</div>
        <div class="mt-1 text-sm text-slate-300">
            Track payments, generate reports, and get insights to grow your rental business.
        </div>
    </div>
</div>
        </div>
    </div>
</div>

@if(isset($featuredTenants) && $featuredTenants->isNotEmpty())
<div class="mt-14">
    <div class="text-center mb-8">
        <h2 class="text-2xl font-bold tracking-tight sm:text-3xl">Featured rental companies</h2>
        <p class="mt-2 text-slate-400 text-sm">Premium partners on RentRide</p>
    </div>
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @foreach($featuredTenants as $ft)
            <div class="rounded-2xl border border-slate-800 bg-slate-950/50 p-5 text-left">
                <div class="text-sm font-semibold text-slate-100">{{ $ft->company_name }}</div>
                @if($ft->address)
                    <div class="mt-2 text-xs text-slate-400 line-clamp-2">{{ $ft->address }}</div>
                @endif
                <a href="{{ route('customer.tenants.vehicles', $ft) }}" class="mt-3 inline-flex text-xs font-semibold text-emerald-300 hover:text-emerald-200">
                    View vehicles →
                </a>
            </div>
        @endforeach
    </div>
</div>
@endif

<div class="mt-14">
    <div class="text-center">
        <h2 id="pricing" class="mt-2 text-2xl font-bold tracking-tight sm:text-4xl">Choose the plan that fits your company</h2>
        <p class="mt-3 text-slate-300">Scroll-friendly pricing cards inspired by modern SaaS sites.</p>
    </div>

    @php
        $planStyles = [
            'basic' => [
                'pillBg' => 'bg-indigo-500/20',
                'pillText' => 'text-indigo-200',
                'border' => 'border-indigo-500/30',
                'bg' => 'from-slate-950 to-indigo-950/30',
                'shadow' => 'shadow-indigo-500/10',
                'ctaBg' => 'bg-indigo-500/20',
                'ctaHover' => 'group-hover:bg-indigo-500/30',
                'ctaText' => 'text-indigo-100',
                'badgeBg' => 'bg-indigo-500/15',
                'badgeText' => 'text-indigo-200',
                'badgeBorder' => 'border-indigo-500/30',
            ],
            'standard' => [
                'pillBg' => 'bg-emerald-500/20',
                'pillText' => 'text-emerald-200',
                'border' => 'border-emerald-500/40',
                'bg' => 'from-slate-950 to-emerald-950/25',
                'shadow' => 'shadow-emerald-500/10',
                'ctaBg' => 'bg-emerald-500',
                'ctaHover' => 'group-hover:bg-emerald-400',
                'ctaText' => 'text-slate-950',
                'badgeBg' => 'bg-emerald-500/10',
                'badgeText' => 'text-emerald-200',
                'badgeBorder' => 'border-emerald-500/30',
            ],
            'premium' => [
                'pillBg' => 'bg-fuchsia-500/20',
                'pillText' => 'text-fuchsia-200',
                'border' => 'border-fuchsia-500/30',
                'bg' => 'from-slate-950 to-fuchsia-950/25',
                'shadow' => 'shadow-fuchsia-500/10',
                'ctaBg' => 'bg-fuchsia-500/20',
                'ctaHover' => 'group-hover:bg-fuchsia-500/30',
                'ctaText' => 'text-fuchsia-100',
                'badgeBg' => 'bg-fuchsia-500/15',
                'badgeText' => 'text-fuchsia-200',
                'badgeBorder' => 'border-fuchsia-500/30',
            ],
        ];

        $defaultPlans = [
            'basic' => ['key' => 'basic', 'name' => 'Basic', 'features' => ['Core booking management','Vehicle listing','Customer records']],
            'standard' => ['key' => 'standard', 'name' => 'Standard', 'features' => ['Everything in Basic','Payment tracking','Sales dashboard']],
            'premium' => ['key' => 'premium', 'name' => 'Premium', 'features' => ['Everything in Standard','Advanced analytics','Maintenance tracking','Auto notifications','Featured listings']],
        ];
    @endphp

    <div class="mt-10 grid grid-cols-1 gap-6 md:grid-cols-3">
        @foreach(['basic','standard','premium'] as $k)
            @php
                $plan = $plans[$k] ?? null;
                $style = $planStyles[$k];

                $name = $plan?->name ?? $defaultPlans[$k]['name'];
                $base = (float) ($plan?->base_price ?? 0);
                $final = $plan ? $plan->discountedPrice() : $base;
                $billing = $plan?->billing_period ?? 'month';

                $features = $plan?->features ?? $defaultPlans[$k]['features'];

                $showDiscount = $plan && $plan->discount_type !== 'none' && (float) $plan->discount_value > 0;
                $discountLabel = null;
                if ($showDiscount) {
                    if ($plan->discount_type === 'percent') {
                        $discountLabel = number_format((float) $plan->discount_value, 0) . '% OFF';
                    } else {
                        $discountLabel = '₱' . number_format((float) $plan->discount_value, 0) . ' OFF';
                    }
                }
            @endphp

            @if($plan)
                <a href="{{ route('tenant.register', ['plan' => $k]) }}" class="group">
            @else
                <div class="group opacity-90" title="This plan is paused — not available for new signups">
            @endif
                <div class="relative h-full flex flex-col overflow-hidden rounded-3xl border {{ $style['border'] }} bg-gradient-to-b {{ $style['bg'] }} p-7 shadow-2xl {{ $style['shadow'] }} transition-transform group-hover:-translate-y-1">
                    <div class="flex items-center justify-between gap-3">
                    <div class="inline-flex items-center rounded-full {{ $style['pillBg'] }} px-4 py-1 text-xs font-semibold {{ $style['pillText'] }}">
                        {{ strtoupper($k) }}
                    </div>
                    <div class="flex items-center gap-2">
                        @if(!empty($mostSubscribedPlan) && $k === $mostSubscribedPlan)
                            <span class="inline-flex items-center rounded-full border {{ $style['badgeBorder'] }} {{ $style['badgeBg'] }} px-3 py-1 text-[11px] font-semibold {{ $style['badgeText'] }}">
                                Most Popular
                            </span>
                        @endif
                        @if($showDiscount)
                            <span class="inline-flex items-center rounded-full border {{ $style['badgeBorder'] }} {{ $style['badgeBg'] }} px-3 py-1 text-[11px] font-semibold {{ $style['badgeText'] }}">
                                {{ $discountLabel }}
                            </span>
                        @endif
                    </div>
                    </div>

                    <div class="mt-6 flex items-baseline gap-3">
                        @if($plan)
                            <div class="text-5xl font-extrabold tracking-tight">₱{{ number_format($final, 0) }}</div>
                            @if($showDiscount)
                                <div class="text-sm text-slate-300 line-through">₱{{ number_format($base, 0) }}</div>
                            @endif
                        @else
                            <div class="text-2xl font-bold tracking-tight text-slate-400">Unavailable</div>
                        @endif
                    </div>
                    @if($plan)
                        <div class="mt-1 text-sm text-slate-300">per {{ $billing }}</div>
                    @endif

                    <div class="mt-6 text-sm font-semibold text-slate-100">What’s included</div>
                    <ul class="mt-3 space-y-2 text-sm text-slate-200">
                        @foreach($features as $f)
                            <li class="flex items-start gap-2"><span class="mt-0.5 h-2 w-2 rounded-full bg-emerald-400"></span>{{ $f }}</li>
                        @endforeach
                    </ul>

                    <div class="mt-auto pt-8">
                        <div class="rounded-xl {{ $style['ctaBg'] }} px-4 py-3 text-center text-sm font-semibold {{ $style['ctaText'] }} {{ $style['ctaHover'] }}">
                            @if($plan)
                                Get {{ $name }} Plan
                            @else
                                Not available for signup
                            @endif
                        </div>
                    </div>
                </div>
            @if($plan)
                </a>
            @else
                </div>
            @endif
        @endforeach

    </div>
</div>
@endsection