<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'RentRide')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /*
         * Avoid layout “jump” when switching routes: short pages hide the vertical scrollbar,
         * long pages show it — the usable width changes and the sticky header looks like it shifts.
         */
        html {
            scrollbar-gutter: stable;
        }
        /* SweetAlert2 — match RentRide slate + emerald UI */
        .swal2-popup {
            background: rgb(30 41 59 / 0.96) !important;
            border: 1px solid rgb(51 65 85) !important;
            border-radius: 1rem !important;
            box-shadow: 0 25px 50px -12px rgb(0 0 0 / 0.55) !important;
            color: #e2e8f0 !important;
        }
        .swal2-title {
            color: #f8fafc !important;
            font-weight: 600 !important;
        }
        .swal2-html-container,
        .swal2-content {
            color: #cbd5e1 !important;
        }
        .swal2-close {
            color: #94a3b8 !important;
        }
        .swal2-close:hover {
            color: #e2e8f0 !important;
        }
        .swal2-confirm {
            background: #10b981 !important;
            color: #0f172a !important;
            border-radius: 0.5rem !important;
            font-weight: 600 !important;
            padding: 0.5rem 1.35rem !important;
            border: none !important;
            box-shadow: 0 10px 15px -3px rgb(16 185 129 / 0.25) !important;
        }
        .swal2-confirm:focus {
            box-shadow: 0 0 0 3px rgb(16 185 129 / 0.35) !important;
        }
        .swal2-cancel {
            background: rgb(51 65 85 / 0.9) !important;
            color: #e2e8f0 !important;
            border: 1px solid rgb(71 85 105) !important;
            border-radius: 0.5rem !important;
            font-weight: 600 !important;
        }
        .swal2-icon.swal2-success {
            border-color: rgba(16, 185, 129, 0.45) !important;
            color: #34d399 !important;
        }
        .swal2-icon.swal2-error {
            border-color: rgba(248, 113, 113, 0.45) !important;
            color: #f87171 !important;
        }
        .swal2-icon.swal2-question,
        .swal2-icon.swal2-info {
            border-color: rgba(56, 189, 248, 0.45) !important;
            color: #38bdf8 !important;
        }
        .swal2-timer-progress-bar {
            background: #10b981 !important;
        }
        .swal2-backdrop {
            background: rgb(15 23 42 / 0.78) !important;
        }
        /*
         * Single-value selects: native OS arrow ignores padding — hide it and draw our own chevron
         * inset from the right edge so it doesn’t touch the border.
         */
        select:not([multiple]):not([size]),
        select.form-select:not([multiple]):not([size]) {
            -webkit-appearance: none !important;
            -moz-appearance: none !important;
            appearance: none !important;
            padding-right: 2.75rem !important;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='20' height='20' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E") !important;
            background-repeat: no-repeat !important;
            background-position: right 0.75rem center !important;
            background-size: 1.1rem 1.1rem !important;
        }
    </style>
    @stack('styles')
</head>
@php
    // Theme customization should apply ONLY to tenant admins.
    $theme = (auth()->check() && auth()->user()->isAdmin())
        ? (auth()->user()->tenant?->theme ?? 'slate')
        : 'slate';

    $themes = [
        'slate' => [
            'text' => 'text-slate-100',
            'bg' => '#0f172a',      // slate-900
            'surface' => '#111827', // slate-900/gray-900-ish
            'surface2' => '#0b1220',// darker
            'border' => '#1f2937',  // slate-800
            'hover' => 'rgba(148,163,184,0.08)',
            'head' => 'rgba(148,163,184,0.10)',
            'headerBorder' => 'rgba(148,163,184,0.15)',
            // Slate theme should feel "blue-gray", not green.
            'brand' => 'bg-sky-400 text-slate-950',
            'accentText' => 'text-sky-300',
            'accentHoverText' => 'hover:text-sky-300',
            'accentBg' => 'bg-sky-400',
            'accentBgHover' => 'hover:bg-sky-300',
            'accentHex' => '#38bdf8',
            'accentHexHover' => '#7dd3fc',
        ],
        'indigo' => [
            'text' => 'text-slate-100',
            'bg' => '#1e1b4b',      // indigo-950
            'surface' => '#111827',
            'surface2' => '#0b1220',
            'border' => '#312e81',  // indigo-900-ish
            'hover' => 'rgba(165,180,252,0.10)',
            'head' => 'rgba(165,180,252,0.12)',
            'headerBorder' => 'rgba(165,180,252,0.20)',
            'brand' => 'bg-indigo-400 text-slate-950',
            'accentText' => 'text-indigo-300',
            'accentHoverText' => 'hover:text-indigo-300',
            'accentBg' => 'bg-indigo-400',
            'accentBgHover' => 'hover:bg-indigo-300',
            'accentHex' => '#818cf8',
            'accentHexHover' => '#a5b4fc',
        ],
        'emerald' => [
            'text' => 'text-slate-100',
            'bg' => '#022c22',      // emerald-950
            'surface' => '#111827',
            'surface2' => '#0b1220',
            'border' => '#064e3b',  // emerald-900
            'hover' => 'rgba(110,231,183,0.10)',
            'head' => 'rgba(110,231,183,0.12)',
            'headerBorder' => 'rgba(110,231,183,0.18)',
            'brand' => 'bg-emerald-400 text-slate-950',
            'accentText' => 'text-emerald-300',
            'accentHoverText' => 'hover:text-emerald-300',
            'accentBg' => 'bg-emerald-400',
            'accentBgHover' => 'hover:bg-emerald-300',
            'accentHex' => '#34d399',
            'accentHexHover' => '#6ee7b7',
        ],
        'fuchsia' => [
            'text' => 'text-slate-100',
            'bg' => '#4a044e',      // fuchsia-950
            'surface' => '#111827',
            'surface2' => '#0b1220',
            'border' => '#701a75',  // fuchsia-900
            'hover' => 'rgba(240,171,252,0.10)',
            'head' => 'rgba(240,171,252,0.12)',
            'headerBorder' => 'rgba(240,171,252,0.18)',
            'brand' => 'bg-fuchsia-400 text-slate-950',
            'accentText' => 'text-fuchsia-200',
            'accentHoverText' => 'hover:text-fuchsia-200',
            'accentBg' => 'bg-fuchsia-400',
            'accentBgHover' => 'hover:bg-fuchsia-300',
            'accentHex' => '#e879f9',
            'accentHexHover' => '#f0abfc',
        ],
    ];

    $t = $themes[$theme] ?? $themes['slate'];

    /**
     * Public entry pages: landing + login/register screens.
     * Same browser session is shared across tabs, so without this, a Super Admin session would
     * still show Profile/Logout on /login, /customer/login, etc. Here we use a compact header instead.
     */
    $isPublicEntryView = request()->is('/')
        || request()->routeIs('login', 'customer.login', 'customer.register', 'tenant.register', 'superadmin.login');
@endphp
<style>
    :root{
        --rr-bg: {{ $t['bg'] }};
        --rr-surface: {{ $t['surface'] }};
        --rr-surface-2: {{ $t['surface2'] }};
        --rr-border: {{ $t['border'] }};
        --rr-row-hover: {{ $t['hover'] }};
        --rr-head: {{ $t['head'] }};
        --rr-header-border: {{ $t['headerBorder'] }};
        --rr-accent: {{ $t['accentHex'] }};
        --rr-accent-hover: {{ $t['accentHexHover'] }};
    }
    .rr-surface { background-color: var(--rr-surface); }
    .rr-surface-2 { background-color: var(--rr-surface-2); }
    .rr-border { border-color: var(--rr-border); }
    .rr-table-head { background-color: var(--rr-head); }
    .rr-row-hover:hover { background-color: var(--rr-row-hover); }

    .rr-btn-primary{
        background: var(--rr-accent);
        color: #020617;
        border: 1px solid transparent;
    }
    .rr-btn-primary:hover{ background: var(--rr-accent-hover); }

    .rr-btn-secondary{
        background: var(--rr-surface-2);
        color: #e2e8f0;
        border: 1px solid var(--rr-border);
    }
    .rr-btn-secondary:hover{ background: rgba(148,163,184,0.08); }

    /* Sub-nav: horizontal scroll — extra padding so dots/badges aren’t clipped */
    .rr-nav-mid {
        -webkit-overflow-scrolling: touch;
        overflow-x: auto;
        padding: 0.75rem 1rem 0.5rem;
    }
    .rr-nav-mid::-webkit-scrollbar { height: 6px; }
    .rr-nav-mid::-webkit-scrollbar-thumb { background: rgba(148,163,184,0.35); border-radius: 6px; }
    .rr-nav-link {
        transition: background-color 0.15s ease, color 0.15s ease;
    }
    .rr-nav-pill {
        position: relative;
        z-index: 1;
    }
    .rr-nav-pill .rr-nav-badge {
        z-index: 2;
    }
</style>
<body class="min-h-screen {{ $t['text'] }}" style="background-color: var(--rr-bg);">
{{-- Top: slim header — logo + profile + logout only --}}
<div class="sticky top-0 z-50">
<header class="border-b backdrop-blur-md" style="border-color: var(--rr-header-border); background-color: rgba(2,6,23,0.95);">
    <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3 sm:px-6">
        @if(request()->routeIs('superadmin.login'))
            <a href="{{ url('/') }}" class="flex items-center gap-3 text-inherit no-underline">
                <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl {{ $t['brand'] }} text-sm font-bold shadow-sm">R</span>
                <span class="text-lg font-semibold tracking-tight">RentRide</span>
            </a>
        @else
            <div class="flex min-w-0 items-center gap-3">
                <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-xl {{ $t['brand'] }} text-sm font-bold shadow-sm">R</span>
                <span class="truncate text-lg font-semibold tracking-tight">RentRide</span>
            </div>
        @endif

        @auth
            <div class="flex shrink-0 items-center gap-2 sm:gap-3">
                @if($isPublicEntryView)
                    {{-- Logged-in elsewhere in this browser: compact actions (not Super Admin Profile row) --}}
                    @if(auth()->user()->isSuperAdmin())
                        <a href="{{ route('superadmin.dashboard') }}" class="rr-nav-link rounded-lg border border-slate-600/80 px-3 py-1.5 text-xs font-medium text-slate-200 hover:bg-slate-800/80 sm:text-sm">Open dashboard</a>
                    @elseif(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="rr-nav-link rounded-lg border border-slate-600/80 px-3 py-1.5 text-xs font-medium text-slate-200 hover:bg-slate-800/80 sm:text-sm">Open dashboard</a>
                    @else
                        <a href="{{ route('customer.dashboard') }}" class="rr-nav-link rounded-lg border border-slate-600/80 px-3 py-1.5 text-xs font-medium text-slate-200 hover:bg-slate-800/80 sm:text-sm">Open dashboard</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf
                        <button type="submit" class="inline-flex items-center rounded-lg border border-slate-600 px-3 py-1.5 text-xs font-semibold text-slate-300 hover:bg-slate-800">Logout</button>
                    </form>
                @else
                    @if(auth()->user()->isSuperAdmin())
                        <a href="{{ route('superadmin.profile') }}" class="rr-nav-link max-w-[7rem] truncate rounded-lg border border-transparent px-2.5 py-1.5 text-xs text-slate-300 hover:border-slate-600 hover:bg-slate-800/50 sm:max-w-[10rem] sm:px-3 sm:text-sm" title="Profile">Profile</a>
                    @elseif(auth()->user()->isAdmin())
                        <a href="{{ route('admin.profile') }}" class="rr-nav-link max-w-[7rem] truncate rounded-lg border border-transparent px-2.5 py-1.5 text-xs text-slate-300 hover:border-slate-600 hover:bg-slate-800/50 sm:max-w-[10rem] sm:px-3 sm:text-sm" title="{{ auth()->user()->name }}">Profile</a>
                    @else
                        @if(auth()->user()->isCustomer())
                            <a href="{{ route('customer.profile') }}" class="rr-nav-link max-w-[7rem] truncate rounded-lg border border-transparent px-2.5 py-1.5 text-xs text-slate-300 hover:border-slate-600 hover:bg-slate-800/50 sm:max-w-[10rem] sm:px-3 sm:text-sm">Profile</a>
                        @endif
                    @endif
                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf
                        <button type="submit" class="rr-btn-primary inline-flex items-center rounded-lg px-3 py-1.5 text-xs font-semibold shadow-sm sm:px-4 sm:text-sm">Logout</button>
                    </form>
                @endif
            </div>
        @endauth
    </div>
</header>

{{-- Below: main navigation — hidden on landing + login/register entry pages --}}
@auth
@if(!$isPublicEntryView)
<nav class="border-b backdrop-blur-sm rr-surface-2" style="border-color: var(--rr-header-border);" aria-label="Main navigation">
    <div class="mx-auto max-w-7xl sm:px-6">
        <div class="rr-nav-mid flex justify-center">
            <div class="inline-flex min-w-min items-center gap-1.5 px-1 pb-0.5 pt-1 sm:gap-2 sm:px-2">
        @if(auth()->user()->isSuperAdmin())
            @php
                $pendingExtCount = \App\Models\PlanExtensionRequest::where('status', 'pending')->count();
                $pendingTenantAppsCount = \App\Models\Tenant::where('status', 'pending')->count();
            @endphp
            <a href="{{ route('superadmin.dashboard') }}" class="rr-nav-pill rr-nav-link shrink-0 whitespace-nowrap rounded-full px-3.5 py-2 text-sm font-medium {{ request()->routeIs('superadmin.dashboard') ? 'bg-slate-700/80 ' . $t['accentText'] . ' ring-1 ring-white/10' : 'text-slate-300 hover:bg-slate-800/70 hover:text-slate-100' }}">Dashboard</a>
            <a href="{{ route('superadmin.tenants.index') }}" class="rr-nav-pill relative inline-flex shrink-0 items-center whitespace-nowrap rounded-full px-3.5 py-2 text-sm font-medium {{ request()->routeIs('superadmin.tenants.*') ? 'bg-slate-700/80 ' . $t['accentText'] . ' ring-1 ring-white/10' : 'text-slate-300 hover:bg-slate-800/70 hover:text-slate-100' }}">
                Tenants
                @if($pendingTenantAppsCount > 0)
                    <span class="rr-nav-badge absolute -right-1 -top-1 h-2.5 w-2.5 rounded-full bg-rose-500 ring-2 ring-slate-900" title="Pending applications"></span>
                @endif
            </a>
            <a href="{{ route('superadmin.plans.index') }}" class="shrink-0 whitespace-nowrap rounded-full px-3.5 py-2 text-sm font-medium {{ request()->routeIs('superadmin.plans.*') ? 'bg-slate-700/80 ' . $t['accentText'] . ' ring-1 ring-white/10' : 'text-slate-300 hover:bg-slate-800/70 hover:text-slate-100' }}">Plans</a>
            <a href="{{ route('superadmin.extensions.index') }}" class="rr-nav-pill relative inline-flex shrink-0 items-center whitespace-nowrap rounded-full px-3.5 py-2 text-sm font-medium {{ request()->routeIs('superadmin.extensions.*') ? 'bg-slate-700/80 ' . $t['accentText'] . ' ring-1 ring-white/10' : 'text-slate-300 hover:bg-slate-800/70 hover:text-slate-100' }}">
                Extensions
                @if($pendingExtCount > 0)
                    <span class="rr-nav-badge absolute -right-1 -top-1 h-2.5 w-2.5 rounded-full bg-rose-500 ring-2 ring-slate-900"></span>
                @endif
            </a>
        @elseif(auth()->user()->isAdmin())
            @php
                $tid = auth()->user()->tenant_id;
                $tenantPendingBookingsCount = $tid
                    ? \App\Models\Booking::where('tenant_id', $tid)->where('status', 'pending')->count()
                    : 0;
                $tenantPendingExtensionCount = $tid
                    ? \App\Models\PlanExtensionRequest::where('tenant_id', $tid)->where('status', 'pending')->count()
                    : 0;
                $tenantDashboardPending = $tenantPendingBookingsCount + $tenantPendingExtensionCount;
                $tn = auth()->user()->tenant;
                $hasMaint = $tn?->hasFeature(\App\Models\Tenant::FEATURE_MAINTENANCE_TRACKING);
                $hasCal = $tn?->hasFeature(\App\Models\Tenant::FEATURE_BOOKING_CALENDAR);
                $hasAnalytics = $tn?->hasFeature(\App\Models\Tenant::FEATURE_ADVANCED_ANALYTICS);
                $hasPay = $tn?->hasFeature(\App\Models\Tenant::FEATURE_PAYMENT_TRACKING);
            @endphp
            <a href="{{ route('admin.dashboard') }}" class="rr-nav-pill relative inline-flex shrink-0 items-center whitespace-nowrap rounded-full px-3.5 py-2 text-sm font-medium {{ request()->routeIs('admin.dashboard') ? 'bg-slate-700/80 ' . $t['accentText'] . ' ring-1 ring-white/10' : 'text-slate-300 hover:bg-slate-800/70 hover:text-slate-100' }}">
                Dashboard
                @if($tenantDashboardPending > 0)
                    <span class="rr-nav-badge absolute -right-1 -top-1 h-2.5 w-2.5 rounded-full bg-rose-500 ring-2 ring-slate-900" title="Needs attention"></span>
                @endif
            </a>
            <a href="{{ route('vehicles.index') }}" class="shrink-0 whitespace-nowrap rounded-full px-3.5 py-2 text-sm font-medium {{ request()->routeIs('vehicles.*') && !request()->routeIs('vehicles.maintenance') ? 'bg-slate-700/80 ' . $t['accentText'] . ' ring-1 ring-white/10' : 'text-slate-300 hover:bg-slate-800/70 hover:text-slate-100' }}">Vehicles</a>
            @if($hasMaint)
                <a href="{{ route('vehicles.maintenance') }}" class="shrink-0 whitespace-nowrap rounded-full px-3.5 py-2 text-sm font-medium {{ request()->routeIs('vehicles.maintenance') ? 'bg-slate-700/80 ' . $t['accentText'] . ' ring-1 ring-white/10' : 'text-slate-300 hover:bg-slate-800/70 hover:text-slate-100' }}">Maintenance</a>
            @endif
            <a href="{{ route('customers.index') }}" class="shrink-0 whitespace-nowrap rounded-full px-3.5 py-2 text-sm font-medium {{ request()->routeIs('customers.*') ? 'bg-slate-700/80 ' . $t['accentText'] . ' ring-1 ring-white/10' : 'text-slate-300 hover:bg-slate-800/70 hover:text-slate-100' }}">Customers</a>
            <a href="{{ route('tenant.reports') }}" class="shrink-0 whitespace-nowrap rounded-full px-3.5 py-2 text-sm font-medium {{ request()->routeIs('tenant.reports') ? 'bg-slate-700/80 ' . $t['accentText'] . ' ring-1 ring-white/10' : 'text-slate-300 hover:bg-slate-800/70 hover:text-slate-100' }}">Reports</a>
            @if($hasAnalytics)
                <a href="{{ route('tenant.analytics') }}" class="shrink-0 whitespace-nowrap rounded-full px-3.5 py-2 text-sm font-medium {{ request()->routeIs('tenant.analytics') ? 'bg-slate-700/80 ' . $t['accentText'] . ' ring-1 ring-white/10' : 'text-slate-300 hover:bg-slate-800/70 hover:text-slate-100' }}">Analytics</a>
            @endif
            <a href="{{ route('bookings.index') }}" class="rr-nav-pill inline-flex shrink-0 items-center gap-1.5 whitespace-nowrap rounded-full px-3.5 py-2 text-sm font-medium {{ request()->routeIs('bookings.index', 'bookings.create', 'bookings.store', 'bookings.updateStatus') ? 'bg-slate-700/80 ' . $t['accentText'] . ' ring-1 ring-white/10' : 'text-slate-300 hover:bg-slate-800/70 hover:text-slate-100' }}">
                <span>Bookings</span>
                @if($tenantPendingBookingsCount > 0)
                    <span class="rr-nav-badge ml-1 inline-flex h-[18px] min-w-[18px] shrink-0 items-center justify-center rounded-full bg-rose-500 px-1 text-[10px] font-bold leading-none text-white ring-2 ring-slate-900">{{ $tenantPendingBookingsCount > 9 ? '9+' : $tenantPendingBookingsCount }}</span>
                @endif
            </a>
            @if($hasCal)
                <a href="{{ route('bookings.calendar') }}" class="shrink-0 whitespace-nowrap rounded-full px-3.5 py-2 text-sm font-medium {{ request()->routeIs('bookings.calendar') ? 'bg-slate-700/80 ' . $t['accentText'] . ' ring-1 ring-white/10' : 'text-slate-300 hover:bg-slate-800/70 hover:text-slate-100' }}">Calendar</a>
            @endif
            @if($hasPay)
                <a href="{{ route('payments.index') }}" class="shrink-0 whitespace-nowrap rounded-full px-3.5 py-2 text-sm font-medium {{ request()->routeIs('payments.*') ? 'bg-slate-700/80 ' . $t['accentText'] . ' ring-1 ring-white/10' : 'text-slate-300 hover:bg-slate-800/70 hover:text-slate-100' }}">Payments</a>
            @endif
        @else
            <a href="{{ route('customer.dashboard') }}" class="shrink-0 whitespace-nowrap rounded-full px-3.5 py-2 text-sm font-medium {{ request()->routeIs('customer.dashboard') ? 'bg-slate-700/80 ' . $t['accentText'] . ' ring-1 ring-white/10' : 'text-slate-300 hover:bg-slate-800/70 hover:text-slate-100' }}">Dashboard</a>
            <a href="{{ route('customer.search') }}" class="shrink-0 whitespace-nowrap rounded-full px-3.5 py-2 text-sm font-medium {{ request()->routeIs('customer.search') ? 'bg-slate-700/80 ' . $t['accentText'] . ' ring-1 ring-white/10' : 'text-slate-300 hover:bg-slate-800/70 hover:text-slate-100' }}">Search cars</a>
            <a href="{{ route('customer.tenants.index') }}" class="shrink-0 whitespace-nowrap rounded-full px-3.5 py-2 text-sm font-medium {{ request()->routeIs('customer.tenants.*') ? 'bg-slate-700/80 ' . $t['accentText'] . ' ring-1 ring-white/10' : 'text-slate-300 hover:bg-slate-800/70 hover:text-slate-100' }}">Companies</a>
        @endif
            </div>
        </div>
    </div>
</nav>
@endif
@endauth
</div>

<main class="mx-auto max-w-6xl px-4 mb-10 pt-6">
    @yield('content')
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: @json(session('success')),
                buttonsStyling: false
            });
        });
    </script>
@endif
@if(session('status'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                icon: 'info',
                title: 'Notice',
                text: @json(session('status')),
                buttonsStyling: false
            });
        });
    </script>
@endif
@if($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                html: '{!! implode('<br>', $errors->all()) !!}',
                buttonsStyling: false
            });
        });
    </script>
@endif
@stack('scripts')
</body>
</html>

