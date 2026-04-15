@extends('layouts.app')

@section('title', 'RentRide — Fleet rental platform')

@section('content')
<div class="relative overflow-hidden rounded-2xl border border-slate-200/90 bg-gradient-to-br from-white via-violet-50/80 to-sky-50/40 shadow-rr sm:rounded-3xl">
    <div class="pointer-events-none absolute inset-0 opacity-50" style="background-image: radial-gradient(circle at 18% 18%, rgba(124,58,237,0.11), transparent 42%), radial-gradient(circle at 82% 28%, rgba(14,165,233,0.09), transparent 38%), radial-gradient(circle at 52% 88%, rgba(251,191,36,0.08), transparent 44%);"></div>
    <div class="relative mx-auto max-w-3xl px-6 py-14 sm:px-10 sm:py-16">
        <p class="inline-flex items-center rounded-full border border-violet-200 bg-violet-50 px-3 py-1.5 text-[11px] font-semibold uppercase tracking-wider text-violet-700">
            Fleet rental platform for operators
        </p>
        <h1 class="mt-6 text-3xl font-extrabold leading-[1.12] tracking-tight text-slate-900 sm:text-5xl sm:leading-[1.08]">
            Operate your rental business on infrastructure built for scale.
        </h1>
        <p class="mt-5 max-w-2xl text-base leading-relaxed text-slate-600 sm:text-lg">
            Vehicles, reservations, customers, and payments—one platform for rental operators.
        </p>

        <div class="mt-10 flex flex-col gap-3 sm:flex-row sm:flex-wrap">
            <a href="#pricing" class="inline-flex h-12 items-center justify-center rounded-xl bg-violet-500 px-6 text-sm font-semibold text-white shadow-lg shadow-violet-500/25 transition hover:bg-violet-400 focus:outline-none focus:ring-2 focus:ring-violet-400/50">
                View plans &amp; pricing
            </a>
            <a href="{{ route('customer.login') }}" class="inline-flex h-12 items-center justify-center rounded-xl bg-amber-400 px-6 text-sm font-semibold text-slate-900 shadow-lg shadow-amber-400/25 transition hover:bg-amber-300 focus:outline-none focus:ring-2 focus:ring-amber-300/50">
                Customer portal
            </a>
            <a href="{{ route('superadmin.login') }}" class="inline-flex h-12 items-center justify-center rounded-xl border border-slate-600/90 bg-slate-900/50 px-6 text-sm font-semibold text-slate-100 backdrop-blur-sm transition hover:border-slate-500 hover:bg-slate-800/70 focus:outline-none focus:ring-2 focus:ring-slate-500/40">
                Platform administration
            </a>
        </div>

        <div class="mt-12 grid gap-4 sm:grid-cols-3">
            <div class="rounded-2xl border border-slate-200 bg-white/90 p-5 shadow-rr-sm backdrop-blur-sm transition duration-200 hover:border-violet-200/90 hover:shadow-md">
                <div class="text-sm font-semibold tracking-tight text-slate-900">Centralized reservations</div>
                <div class="mt-2 text-sm leading-relaxed text-slate-600">
                    Coordinate schedules, availability, and customer records through a single operational view.
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white/90 p-5 shadow-rr-sm backdrop-blur-sm transition duration-200 hover:border-violet-200/90 hover:shadow-md">
                <div class="text-sm font-semibold tracking-tight text-slate-900">Fleet &amp; asset visibility</div>
                <div class="mt-2 text-sm leading-relaxed text-slate-600">
                    Monitor utilization, status, and maintenance signals with timely updates across your fleet.
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white/90 p-5 shadow-rr-sm backdrop-blur-sm transition duration-200 hover:border-violet-200/90 hover:shadow-md">
                <div class="text-sm font-semibold tracking-tight text-slate-900">Revenue &amp; reporting</div>
                <div class="mt-2 text-sm leading-relaxed text-slate-600">
                    Reconcile payments, export insights, and support financial oversight with structured reporting.
                </div>
            </div>
        </div>
    </div>
</div>

@if(isset($featuredTenants) && $featuredTenants->isNotEmpty())
<div class="mt-16 sm:mt-20">
    <div class="mx-auto mb-10 max-w-2xl text-center">
        <h2 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Featured operators</h2>
    </div>
    <div class="mx-auto grid max-w-6xl gap-5 sm:grid-cols-2 lg:grid-cols-4">
        @foreach($featuredTenants as $ft)
            <div class="group relative mt-10 overflow-visible rounded-3xl border border-slate-200 bg-white/95 text-center shadow-rr-sm transition duration-200 hover:-translate-y-1 hover:border-violet-200 hover:shadow-lg">
                <div class="absolute left-1/2 top-0 -translate-x-1/2 -translate-y-1/2">
                    <div class="h-20 w-20 overflow-hidden rounded-2xl border-4 border-white bg-white shadow-lg shadow-violet-100 ring-1 ring-violet-200/70">
                        @if(!empty($ft->logo_path))
                            <img src="{{ asset('storage/' . $ft->logo_path) }}" alt="{{ $ft->company_name }} logo" class="h-full w-full object-cover">
                        @else
                            <div class="flex h-full w-full items-center justify-center bg-violet-50 text-2xl font-bold text-violet-700">
                                {{ strtoupper(substr($ft->company_name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                </div>

                <div class="rounded-t-3xl bg-gradient-to-r from-violet-50 via-white to-sky-50 px-5 pb-4 pt-12">
                    <div class="mx-auto max-w-[14rem] truncate text-base font-semibold tracking-tight text-slate-900">
                        {{ $ft->company_name }}
                    </div>
                    <p class="mt-1 text-[11px] font-semibold uppercase tracking-wider text-violet-700/80">Featured operator</p>
                </div>

                <div class="flex h-full flex-col border-t border-slate-200/70 px-5 pb-5 pt-4 text-left">
                    @if(!empty($ft->public_tagline))
                        <div class="line-clamp-2 min-h-[2.5rem] text-sm font-medium leading-relaxed text-slate-700">{{ $ft->public_tagline }}</div>
                    @else
                        <div class="min-h-[2.5rem] text-sm leading-relaxed text-slate-500">Trusted rental partner with a growing vehicle lineup.</div>
                    @endif

                    <div class="mt-3 rounded-xl border border-slate-200/80 bg-slate-50/80 px-3 py-2.5 text-xs leading-relaxed text-slate-600">
                        @if($ft->address)
                            <div class="line-clamp-2">{{ $ft->address }}</div>
                        @else
                            <div>Address not provided</div>
                        @endif
                    </div>

                    <a href="{{ route('customer.tenants.vehicles', $ft) }}" class="mt-4 inline-flex w-full items-center justify-center rounded-xl bg-violet-600 px-3 py-2.5 text-xs font-semibold text-white transition hover:bg-violet-500">
                        Browse fleet
                    </a>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif

<div class="mt-16 sm:mt-20">
    <div class="mx-auto max-w-2xl text-center">
        <h2 id="pricing" class="scroll-mt-28 text-2xl font-bold tracking-tight text-slate-900 sm:text-4xl">Plans &amp; pricing</h2>
        <p class="mt-4 text-sm text-slate-500 sm:text-base">Compare tiers and pricing.</p>
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

    <div class="mx-auto mt-12 grid max-w-6xl grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
        @forelse($plans as $plan)
            @php
                $ft = $plan->feature_tier ?? 'basic';
                if (! in_array($ft, ['basic', 'standard', 'premium'], true)) {
                    $ft = 'basic';
                }
                $style = $planStyles[$ft] ?? $planStyles['basic'];
                $name = $plan->name;
                $base = (float) $plan->base_price;
                $final = $plan->discountedPrice();
                $billing = $plan->billing_period ?? 'month';
                $pillLabel = $plan->tier ?: ucfirst($ft);
                $features = $plan->features ?? ($defaultPlans[$ft]['features'] ?? $defaultPlans['basic']['features']);
                $showDiscount = $plan->discount_type !== 'none' && (float) $plan->discount_value > 0;
                $discountLabel = null;
                if ($showDiscount) {
                    if ($plan->discount_type === 'percent') {
                        $discountLabel = number_format((float) $plan->discount_value, 0) . '% OFF';
                    } else {
                        $discountLabel = '₱' . number_format((float) $plan->discount_value, 0) . ' OFF';
                    }
                }
                $registrationOpen = $plan->is_active;
            @endphp

            @if($registrationOpen)
                <a href="{{ route('tenant.register', ['plan' => $plan->key]) }}" class="group">
            @else
                <div class="group">
            @endif
                <div class="relative flex h-full flex-col overflow-hidden rounded-3xl border {{ $style['border'] }} bg-gradient-to-b {{ $style['bg'] }} p-7 shadow-lg {{ $style['shadow'] }} transition duration-200 @if($registrationOpen) group-hover:-translate-y-0.5 group-hover:shadow-xl @endif {{ $registrationOpen ? '' : 'opacity-95' }}">
                    <div class="flex items-center justify-between gap-3">
                    <div class="inline-flex items-center rounded-full {{ $style['pillBg'] }} px-4 py-1 text-[11px] font-bold uppercase tracking-widest {{ $style['pillText'] }}">
                        {{ strtoupper($pillLabel) }}
                    </div>
                    <div class="flex flex-wrap items-center justify-end gap-2">
                        @if(!$registrationOpen)
                            <span class="inline-flex items-center rounded-full border border-slate-300 bg-slate-100 px-3 py-1 text-[11px] font-semibold text-slate-600">
                                Disabled
                            </span>
                        @endif
                        @if(!empty($mostSubscribedPlan) && $plan->key === $mostSubscribedPlan)
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
                        <div class="text-5xl font-extrabold tabular-nums tracking-tight text-slate-900">₱{{ number_format($final, 0) }}</div>
                        @if($showDiscount)
                            <div class="text-sm tabular-nums text-slate-500 line-through">₱{{ number_format($base, 0) }}</div>
                        @endif
                    </div>
                    <div class="mt-1 text-sm text-slate-500">per {{ $billing }}</div>

                    <div class="mt-6 text-xs font-bold uppercase tracking-wider text-slate-500">Plan highlights</div>
                    <ul class="mt-3 space-y-2.5 text-sm leading-snug text-slate-600">
                        @foreach($features as $f)
                            <li class="flex gap-2.5"><span class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-violet-400/90" aria-hidden="true"></span><span>{{ $f }}</span></li>
                        @endforeach
                    </ul>

                    <div class="mt-auto pt-8">
                        <div class="rounded-xl px-4 py-3.5 text-center text-sm font-semibold transition {{ $registrationOpen ? $style['ctaBg'] . ' ' . $style['ctaText'] . ' ' . $style['ctaHover'] : 'border border-slate-300 bg-slate-200 text-slate-600' }}">
                            @if($registrationOpen)
                                Get started — {{ $name }}
                            @else
                                Disabled
                            @endif
                        </div>
                    </div>
                </div>
            @if($registrationOpen)
                </a>
            @else
                </div>
            @endif
        @empty
            <p class="col-span-full text-center text-sm text-slate-500">No plans available.</p>
        @endforelse

    </div>
</div>
@endsection
