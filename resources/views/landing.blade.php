@extends('layouts.app')

@section('title', 'RentRide - Multi-tenant Rental Platform')

@section('content')
<div class="relative overflow-hidden rounded-2xl border border-slate-200/90 bg-gradient-to-br from-white via-violet-50/80 to-sky-50/40 shadow-rr sm:rounded-3xl">
    <div class="pointer-events-none absolute inset-0 opacity-50" style="background-image: radial-gradient(circle at 18% 18%, rgba(124,58,237,0.11), transparent 42%), radial-gradient(circle at 82% 28%, rgba(14,165,233,0.09), transparent 38%), radial-gradient(circle at 52% 88%, rgba(251,191,36,0.08), transparent 44%);"></div>
    <div class="relative mx-auto max-w-3xl px-6 py-14 sm:px-10 sm:py-16">
        <p class="inline-flex items-center rounded-full border border-violet-200 bg-violet-50 px-3 py-1.5 text-[11px] font-semibold uppercase tracking-wider text-violet-700">
            Multi-tenant car rental management system
        </p>
        <h1 class="mt-6 text-3xl font-extrabold leading-[1.12] tracking-tight text-slate-900 sm:text-5xl sm:leading-[1.08]">
            Run your rental business faster with a platform built for tenants.
        </h1>
        <p class="mt-5 max-w-2xl text-base leading-relaxed text-slate-600 sm:text-lg">
            RentRide helps car rental businesses manage vehicles, bookings, customers, and payments in one powerful platform while giving you full control over your operations and growth.
        </p>

        <div class="mt-10 flex flex-col gap-3 sm:flex-row sm:flex-wrap">
            <a href="#pricing" class="inline-flex h-12 items-center justify-center rounded-xl bg-violet-500 px-6 text-sm font-semibold text-white shadow-lg shadow-violet-500/25 transition hover:bg-violet-400 focus:outline-none focus:ring-2 focus:ring-violet-400/50">
                View subscription plans
            </a>
            <a href="{{ route('customer.login') }}" class="inline-flex h-12 items-center justify-center rounded-xl bg-amber-400 px-6 text-sm font-semibold text-slate-900 shadow-lg shadow-amber-400/25 transition hover:bg-amber-300 focus:outline-none focus:ring-2 focus:ring-amber-300/50">
                Rent a car
            </a>
            <a href="{{ route('superadmin.login') }}" class="inline-flex h-12 items-center justify-center rounded-xl border border-slate-600/90 bg-slate-900/50 px-6 text-sm font-semibold text-slate-100 backdrop-blur-sm transition hover:border-slate-500 hover:bg-slate-800/70 focus:outline-none focus:ring-2 focus:ring-slate-500/40">
                Super Admin Login
            </a>
        </div>

        <div class="mt-12 grid gap-4 sm:grid-cols-3">
            <div class="rounded-2xl border border-slate-200 bg-white/90 p-5 shadow-rr-sm backdrop-blur-sm transition duration-200 hover:border-violet-200/90 hover:shadow-md">
                <div class="text-sm font-semibold tracking-tight text-slate-900">Easy Booking Management</div>
                <div class="mt-2 text-sm leading-relaxed text-slate-600">
                    Manage reservations, schedules, and customer bookings in one simple dashboard.
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white/90 p-5 shadow-rr-sm backdrop-blur-sm transition duration-200 hover:border-violet-200/90 hover:shadow-md">
                <div class="text-sm font-semibold tracking-tight text-slate-900">Vehicle & Fleet Tracking</div>
                <div class="mt-2 text-sm leading-relaxed text-slate-600">
                    Monitor vehicle availability, status, and maintenance with real-time updates.
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white/90 p-5 shadow-rr-sm backdrop-blur-sm transition duration-200 hover:border-violet-200/90 hover:shadow-md">
                <div class="text-sm font-semibold tracking-tight text-slate-900">Automated Payments & Reports</div>
                <div class="mt-2 text-sm leading-relaxed text-slate-600">
                    Track payments, generate reports, and get insights to grow your rental business.
                </div>
            </div>
        </div>
    </div>
</div>

@if(isset($featuredTenants) && $featuredTenants->isNotEmpty())
<div class="mt-16 sm:mt-20">
    <div class="mx-auto mb-10 max-w-2xl text-center">
        <h2 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Featured rental companies</h2>
        <p class="mt-3 text-sm leading-relaxed text-slate-600">Premium partners on RentRide</p>
    </div>
    <div class="mx-auto grid max-w-6xl gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @foreach($featuredTenants as $ft)
            <div class="rounded-2xl border border-slate-200 bg-white p-5 text-left shadow-rr-sm transition hover:border-violet-200/80 hover:shadow-md">
                <div class="text-sm font-semibold tracking-tight text-slate-900">{{ $ft->company_name }}</div>
                @if($ft->address)
                    <div class="mt-2 line-clamp-2 text-xs leading-relaxed text-slate-600">{{ $ft->address }}</div>
                @endif
                <a href="{{ route('customer.tenants.vehicles', $ft) }}" class="mt-4 inline-flex items-center text-xs font-semibold text-violet-700 transition hover:text-violet-600">
                    View vehicles
                    <span class="ml-1" aria-hidden="true">→</span>
                </a>
            </div>
        @endforeach
    </div>
</div>
@endif

<div class="mt-16 sm:mt-20">
    <div class="mx-auto max-w-2xl text-center">
        <h2 id="pricing" class="scroll-mt-28 text-2xl font-bold tracking-tight text-slate-900 sm:text-4xl">Choose the plan that fits your company</h2>
        <p class="mt-4 text-sm leading-relaxed text-slate-400 sm:text-base">Scroll-friendly pricing cards inspired by modern SaaS sites.</p>
    </div>

    @php
        $planStyles = [
            'basic' => [
                'pillBg' => 'bg-indigo-100',
                'pillText' => 'text-indigo-700',
                'border' => 'border-indigo-200',
                'bg' => 'from-white to-indigo-50/70',
                'shadow' => 'shadow-indigo-100',
                'ctaBg' => 'bg-indigo-600',
                'ctaHover' => 'group-hover:bg-indigo-500',
                'ctaText' => 'text-white',
                'badgeBg' => 'bg-indigo-50',
                'badgeText' => 'text-indigo-700',
                'badgeBorder' => 'border-indigo-200',
            ],
            'standard' => [
                'pillBg' => 'bg-violet-100',
                'pillText' => 'text-violet-700',
                'border' => 'border-violet-300',
                'bg' => 'from-white to-violet-50/75',
                'shadow' => 'shadow-violet-100',
                'ctaBg' => 'bg-violet-600',
                'ctaHover' => 'group-hover:bg-violet-500',
                'ctaText' => 'text-white',
                'badgeBg' => 'bg-violet-50',
                'badgeText' => 'text-violet-700',
                'badgeBorder' => 'border-violet-200',
            ],
            'premium' => [
                'pillBg' => 'bg-amber-100',
                'pillText' => 'text-amber-700',
                'border' => 'border-amber-200',
                'bg' => 'from-white to-amber-50/80',
                'shadow' => 'shadow-amber-100',
                'ctaBg' => 'bg-amber-500',
                'ctaHover' => 'group-hover:bg-amber-400',
                'ctaText' => 'text-slate-900',
                'badgeBg' => 'bg-amber-50',
                'badgeText' => 'text-amber-700',
                'badgeBorder' => 'border-amber-200',
            ],
        ];

        $defaultPlans = [
            'basic' => ['key' => 'basic', 'name' => 'Basic', 'features' => ['Core booking management','Vehicle listing','Customer records']],
            'standard' => ['key' => 'standard', 'name' => 'Standard', 'features' => ['Everything in Basic','Payment tracking','Sales dashboard']],
            'premium' => ['key' => 'premium', 'name' => 'Premium', 'features' => ['Everything in Standard','Advanced analytics','Maintenance tracking','Auto notifications','Featured listings']],
        ];
    @endphp

    <div class="mx-auto mt-12 grid max-w-6xl grid-cols-1 gap-6 md:grid-cols-3">
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
                <div class="relative flex h-full flex-col overflow-hidden rounded-3xl border {{ $style['border'] }} bg-gradient-to-b {{ $style['bg'] }} p-7 shadow-lg {{ $style['shadow'] }} transition duration-200 group-hover:-translate-y-0.5 group-hover:shadow-xl">
                    <div class="flex items-center justify-between gap-3">
                    <div class="inline-flex items-center rounded-full {{ $style['pillBg'] }} px-4 py-1 text-[11px] font-bold uppercase tracking-widest {{ $style['pillText'] }}">
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

                    <div class="mt-6 flex flex-wrap items-baseline gap-2">
                        @if($plan)
                            <div class="text-5xl font-extrabold tabular-nums tracking-tight text-slate-900">₱{{ number_format($final, 0) }}</div>
                            @if($showDiscount)
                                <div class="text-sm tabular-nums text-slate-500 line-through">₱{{ number_format($base, 0) }}</div>
                            @endif
                        @else
                            <div class="text-2xl font-bold tracking-tight text-slate-500">Unavailable</div>
                        @endif
                    </div>
                    @if($plan)
                        <div class="mt-1 text-sm text-slate-500">per {{ $billing }}</div>
                    @endif

                    <div class="mt-6 text-xs font-bold uppercase tracking-wider text-slate-500">What’s included</div>
                    <ul class="mt-3 space-y-2.5 text-sm leading-snug text-slate-600">
                        @foreach($features as $f)
                            <li class="flex gap-2.5"><span class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-violet-400/90" aria-hidden="true"></span><span>{{ $f }}</span></li>
                        @endforeach
                    </ul>

                    <div class="mt-auto pt-8">
                        <div class="rounded-xl {{ $style['ctaBg'] }} px-4 py-3.5 text-center text-sm font-semibold {{ $style['ctaText'] }} {{ $style['ctaHover'] }} transition">
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
