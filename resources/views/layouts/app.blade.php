<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'RentRide')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'ui-sans-serif', 'system-ui', 'Segoe UI', 'sans-serif'],
                    },
                    boxShadow: {
                        'rr': '0 4px 24px -4px rgba(15,23,42,0.12), 0 0 0 1px rgba(148,163,184,0.08)',
                        'rr-sm': '0 2px 12px -2px rgba(15,23,42,0.1)',
                    },
                },
            },
        };
    </script>
    <style>
        /*
         * Always reserve vertical scrollbar width so route changes don’t change the layout width.
         * (Do not combine with SweetAlert’s scrollbar padding — use overflow-y here + default Swal padding.)
         */
        html {
            overflow-y: scroll;
        }
        /*
         * Shared UI — forms & cards (works with Tailwind CDN; no Bootstrap)
         */
        body {
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            text-rendering: optimizeLegibility;
        }
        .rr-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--rr-text-secondary, #475569);
            margin-bottom: 0.375rem;
        }
        .rr-label-sm {
            display: block;
            font-size: 0.75rem;
            font-weight: 500;
            color: var(--rr-text-muted, #64748b);
            margin-bottom: 0.25rem;
        }
        .rr-input,
        .rr-textarea,
        select.rr-input {
            width: 100%;
            border-radius: 0.5rem;
            border: 1px solid var(--rr-input-border, #d0d6e0);
            background: var(--rr-input-bg, #ffffff);
            color: var(--rr-text, #0f172a);
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
            line-height: 1.5;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }
        .rr-input::placeholder,
        .rr-textarea::placeholder {
            color: var(--rr-text-placeholder, #94a3b8);
        }
        .rr-input:focus,
        .rr-textarea:focus,
        select.rr-input:focus {
            outline: none;
            border-color: var(--rr-accent);
            box-shadow: 0 0 0 3px color-mix(in srgb, var(--rr-accent) 28%, transparent);
        }
        .rr-textarea {
            min-height: 5rem;
            resize: vertical;
        }
        input[type="file"].rr-file {
            display: block;
            width: 100%;
            font-size: 0.8125rem;
            color: var(--rr-text-secondary, #475569);
        }
        input[type="file"].rr-file::file-selector-button {
            margin-right: 0.75rem;
            border-radius: 0.375rem;
            border: 0;
            background: var(--rr-surface-2, #f1f5f9);
            padding: 0.375rem 0.75rem;
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--rr-text, #0f172a);
            cursor: pointer;
        }
        input[type="file"].rr-file::file-selector-button:hover {
            background: color-mix(in srgb, var(--rr-text-muted, #64748b) 14%, var(--rr-surface-2, #f1f5f9));
        }
        .rr-panel {
            border-radius: 0.75rem;
            border: 1px solid var(--rr-border, #e2e8f0);
            background: var(--rr-surface, #ffffff);
            box-shadow: 0 4px 20px -10px rgba(15, 23, 42, 0.1);
        }
        .rr-panel-elevated {
            border-radius: 0.75rem;
            border: 1px solid var(--rr-border, #e2e8f0);
            background: var(--rr-surface, #ffffff);
            box-shadow: 0 12px 32px -16px rgba(15, 23, 42, 0.12), 0 0 0 1px rgba(148, 163, 184, 0.06);
        }
        /* SweetAlert2 — light theme to match app */
        .swal2-popup {
            background: var(--rr-surface, #ffffff) !important;
            border: 1px solid var(--rr-border, #e2e8f0) !important;
            border-radius: 1rem !important;
            box-shadow: 0 25px 50px -18px rgb(15 23 42 / 0.18), 0 0 0 1px rgba(148, 163, 184, 0.08) !important;
            color: var(--rr-text, #0f172a) !important;
        }
        .swal2-title {
            color: var(--rr-text, #0f172a) !important;
            font-weight: 600 !important;
        }
        .swal2-html-container,
        .swal2-content {
            color: var(--rr-text-secondary, #475569) !important;
        }
        .swal2-close {
            color: var(--rr-text-muted, #64748b) !important;
        }
        .swal2-close:hover {
            color: var(--rr-text, #0f172a) !important;
        }
        .swal2-confirm {
            background: var(--rr-accent) !important;
            color: #0f172a !important;
            border-radius: 0.5rem !important;
            font-weight: 600 !important;
            padding: 0.5rem 1.35rem !important;
            border: none !important;
            box-shadow: 0 10px 15px -5px color-mix(in srgb, var(--rr-accent) 35%, transparent) !important;
        }
        .swal2-confirm:focus {
            box-shadow: 0 0 0 3px color-mix(in srgb, var(--rr-accent) 35%, transparent) !important;
        }
        .swal2-cancel {
            background: var(--rr-surface-2, #f1f5f9) !important;
            color: var(--rr-text-secondary, #475569) !important;
            border: 1px solid var(--rr-border, #e2e8f0) !important;
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
            background: var(--rr-accent) !important;
        }
        .swal2-backdrop {
            background: color-mix(in srgb, var(--rr-text, #0f172a) 38%, transparent) !important;
        }
        /*
         * Single-value selects: native OS arrow ignores padding — hide it and draw our own chevron
         * inset from the right edge so it doesn’t touch the border.
         */
        select:not([multiple]):not([size]) {
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
    $host = request()->getHost();
    $isCentralHost = in_array($host, config('tenancy.central_domains', []), true);
    $headerTenant = null;
    if (auth()->check() && auth()->user()->isTenantUser()) {
        $headerTenant = auth()->user()->tenant;
    } elseif (! $isCentralHost) {
        $headerTenant = \App\Models\Tenant::where('domain', $host)->first();
    } elseif (request()->filled('tenant')) {
        $tk = trim((string) request()->query('tenant'));
        if ($tk !== '') {
            if (ctype_digit($tk)) {
                $headerTenant = \App\Models\Tenant::find((int) $tk);
            } else {
                $slug = \Illuminate\Support\Str::slug($tk);
                if ($slug !== '') {
                    $headerTenant = \App\Models\Tenant::where('slug', $slug)->first();
                }
            }
        }
    }

    // Theme accent: logged-in tenant users, or guest pages branded to a tenant (login on tenant domain / ?tenant=).
    $theme = 'slate';
    if (auth()->check() && auth()->user()->isTenantUser()) {
        $theme = auth()->user()->tenant?->theme ?? 'slate';
    } elseif ($headerTenant) {
        $theme = $headerTenant->theme ?? 'slate';
    }

    $themes = [
        'slate' => [
            'text' => 'text-slate-800',
            'bg' => '#f1f5f9',
            'surface' => '#ffffff',
            'surface2' => '#f8fafc',
            'border' => '#dbe3ee',
            'hover' => 'rgba(30,41,59,0.045)',
            'head' => 'rgba(148,163,184,0.12)',
            'headerBorder' => 'rgba(148,163,184,0.22)',
            'brand' => 'bg-sky-400 text-slate-950',
            'accentText' => 'text-sky-300',
            'accentHoverText' => 'hover:text-sky-300',
            'accentBg' => 'bg-sky-400',
            'accentBgHover' => 'hover:bg-sky-300',
            'accentHex' => '#38bdf8',
            'accentHexHover' => '#7dd3fc',
        ],
        'indigo' => [
            'text' => 'text-slate-800',
            'bg' => '#eef2ff',
            'surface' => '#ffffff',
            'surface2' => '#eef2ff',
            'border' => '#c7d2fe',
            'hover' => 'rgba(99,102,241,0.08)',
            'head' => 'rgba(99,102,241,0.10)',
            'headerBorder' => 'rgba(99,102,241,0.20)',
            'brand' => 'bg-indigo-400 text-slate-950',
            'accentText' => 'text-indigo-300',
            'accentHoverText' => 'hover:text-indigo-300',
            'accentBg' => 'bg-indigo-400',
            'accentBgHover' => 'hover:bg-indigo-300',
            'accentHex' => '#818cf8',
            'accentHexHover' => '#a5b4fc',
        ],
        'emerald' => [
            'text' => 'text-slate-800',
            'bg' => '#ecfdf5',
            'surface' => '#ffffff',
            'surface2' => '#ecfdf5',
            'border' => '#a7f3d0',
            'hover' => 'rgba(16,185,129,0.08)',
            'head' => 'rgba(16,185,129,0.10)',
            'headerBorder' => 'rgba(16,185,129,0.18)',
            'brand' => 'bg-emerald-400 text-slate-950',
            'accentText' => 'text-emerald-300',
            'accentHoverText' => 'hover:text-emerald-300',
            'accentBg' => 'bg-emerald-400',
            'accentBgHover' => 'hover:bg-emerald-300',
            'accentHex' => '#34d399',
            'accentHexHover' => '#6ee7b7',
        ],
        'fuchsia' => [
            'text' => 'text-slate-800',
            'bg' => '#fdf2f8',
            'surface' => '#ffffff',
            'surface2' => '#fdf2f8',
            'border' => '#f5c2e7',
            'hover' => 'rgba(217,70,239,0.08)',
            'head' => 'rgba(217,70,239,0.10)',
            'headerBorder' => 'rgba(217,70,239,0.18)',
            'brand' => 'bg-fuchsia-400 text-slate-950',
            'accentText' => 'text-fuchsia-200',
            'accentHoverText' => 'hover:text-fuchsia-200',
            'accentBg' => 'bg-fuchsia-400',
            'accentBgHover' => 'hover:bg-fuchsia-300',
            'accentHex' => '#e879f9',
            'accentHexHover' => '#f0abfc',
        ],
    ];

    // Tenant-selected theme controls full background/surface palette and accents.
    $selectedTheme = $themes[$theme] ?? $themes['slate'];
    $t = $selectedTheme;

    /**
     * Public entry pages: landing + login/register screens.
     * Same browser session is shared across tabs, so without this, a Super Admin session would
     * still show Profile/Logout on /login, /customer/login, etc. Here we use a compact header instead.
     */
    $isPublicEntryView = request()->is('/')
        || request()->routeIs('login', 'customer.login', 'customer.register', 'tenant.register', 'superadmin.login', 'tenant.login', 'tenant.password.request', 'tenant.password.reset.verify', 'tenant.password.reset');

    $showRentRideBrand = request()->is('/') || request()->routeIs('superadmin.*', 'superadmin.login');
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
        --rr-text: #0f172a;
        --rr-text-secondary: #475569;
        --rr-text-muted: #64748b;
        --rr-text-placeholder: #94a3b8;
        --rr-input-bg: #ffffff;
        --rr-input-border: #d0d6e0;
        --rr-nav-active-bg: color-mix(in srgb, var(--rr-accent) 11%, var(--rr-surface));
        --rr-nav-active-border: color-mix(in srgb, var(--rr-accent) 28%, var(--rr-border));
    }
    /* Same as body — avoids a bright strip next to the scrollbar when modals lock scroll */
    html {
        background-color: var(--rr-bg);
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
    .rr-link-accent { color: var(--rr-accent); }
    .rr-link-accent:hover { color: var(--rr-accent-hover); }
    .rr-chip-accent {
        background: color-mix(in srgb, var(--rr-accent) 16%, transparent);
        color: color-mix(in srgb, var(--rr-accent) 82%, white 18%);
        border: 1px solid color-mix(in srgb, var(--rr-accent) 38%, transparent);
    }

    .rr-btn-secondary{
        background: var(--rr-surface-2);
        color: #334155;
        border: 1px solid var(--rr-border);
    }
    .rr-btn-secondary:hover{
        background: color-mix(in srgb, var(--rr-accent) 8%, var(--rr-surface-2));
        border-color: color-mix(in srgb, var(--rr-accent) 22%, var(--rr-border));
    }

    /* Main nav — active pill picks up tenant accent */
    nav[aria-label="Main navigation"] a.bg-white {
        background: var(--rr-nav-active-bg) !important;
        border-color: var(--rr-nav-active-border) !important;
        color: var(--rr-text) !important;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.05) !important;
    }
    nav[aria-label="Main navigation"] a.border-transparent:hover {
        background: color-mix(in srgb, var(--rr-accent) 6%, var(--rr-surface-2)) !important;
    }

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

    .rr-header-bar {
        border-bottom: 1px solid var(--rr-header-border);
        background: linear-gradient(
            180deg,
            color-mix(in srgb, var(--rr-surface) 94%, transparent) 0%,
            color-mix(in srgb, var(--rr-surface) 88%, var(--rr-bg)) 100%
        );
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
    }
</style>
<body class="min-h-screen font-sans {{ $t['text'] }}" style="background-color: var(--rr-bg); background-image: radial-gradient(ellipse 120% 80% at 50% -20%, color-mix(in srgb, var(--rr-accent) 7%, transparent), transparent 55%);">
<style>
    /* Light UI remap for pages still using dark Tailwind utility classes. */
    [class*="text-slate-50"], [class*="text-slate-100"], [class*="text-slate-200"] { color: var(--rr-text) !important; }
    [class*="text-slate-300"] { color: var(--rr-text-secondary) !important; }
    [class*="text-slate-400"] { color: var(--rr-text-secondary) !important; }
    [class*="text-slate-500"] { color: var(--rr-text-muted) !important; }
    [class*="bg-slate-900"], [class*="bg-slate-950"], [class*="bg-slate-800"] { background-color: var(--rr-surface) !important; }
    [class*="border-slate-700"], [class*="border-slate-800"], [class*="border-slate-600"] { border-color: var(--rr-border) !important; }
    [class*="shadow-2xl"], [class*="shadow-xl"], [class*="shadow-lg"], [class*="shadow-rr"], [class*="shadow-rr-sm"] { box-shadow: 0 12px 32px -18px rgba(15, 23, 42, 0.14), 0 0 0 1px rgba(148, 163, 184, 0.06) !important; }

    /* Consistent light tables across all modules */
    table { color: var(--rr-text) !important; }
    thead, .rr-table-head {
        background: var(--rr-surface-2) !important;
        color: var(--rr-text-secondary) !important;
        border-bottom: 1px solid var(--rr-border) !important;
    }
    tbody tr, .rr-row-hover {
        background: var(--rr-surface) !important;
    }
    tbody tr:hover, .rr-row-hover:hover {
        background: color-mix(in srgb, var(--rr-accent) 4%, var(--rr-surface)) !important;
    }
    tbody, thead, tr, th, td {
        border-color: var(--rr-border) !important;
    }

    /* Generic card/panel surfaces that may still carry dark utility combos */
    .rounded-xl, .rounded-2xl, .rounded-3xl {
        border-color: var(--rr-border);
    }

    /* Keep chips readable on white backgrounds */
    [class*="bg-amber-500/"], [class*="bg-amber-400/"] { color: #92400e !important; border-color: #fcd34d !important; }
    [class*="bg-rose-500/"] { color: #9f1239 !important; border-color: #fda4af !important; }
    [class*="bg-emerald-500/"], [class*="bg-emerald-400/"] { color: #065f46 !important; border-color: #86efac !important; }
    [class*="bg-sky-500/"] { color: #075985 !important; border-color: #7dd3fc !important; }
    [class*="bg-fuchsia-500/"], [class*="bg-violet-500/"] { color: #5b21b6 !important; border-color: #c4b5fd !important; }
</style>
{{-- Top: slim header — logo + profile + logout only --}}
<div class="sticky top-0 z-50">
<header class="rr-header-bar backdrop-blur-md">
    <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3 sm:px-6">
        @if($showRentRideBrand || ! $headerTenant)
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
        @else
            <div class="flex min-w-0 items-center gap-3">
                @if(!empty($headerTenant->logo_path))
                    <img src="{{ asset('storage/' . $headerTenant->logo_path) }}" alt="{{ $headerTenant->company_name }} logo" class="h-9 w-9 shrink-0 rounded-xl object-cover ring-1 ring-white/10 shadow-sm">
                @else
                    <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-xl {{ $t['brand'] }} text-sm font-bold shadow-sm">{{ strtoupper(substr($headerTenant->company_name, 0, 1)) }}</span>
                @endif
                <span class="truncate text-lg font-semibold tracking-tight">{{ $headerTenant->company_name }}</span>
            </div>
        @endif

        @auth
            <div class="flex shrink-0 items-center gap-2 sm:gap-3">
                @if($isPublicEntryView)
                    {{-- Logged-in elsewhere in this browser: compact actions (not Super Admin Profile row) --}}
                    @if(auth()->user()->isSuperAdmin())
                        <a href="{{ route('superadmin.dashboard') }}" class="rr-nav-link rounded-lg border border-slate-600/80 px-3 py-1.5 text-xs font-medium text-slate-200 hover:bg-slate-800/80 sm:text-sm">Open dashboard</a>
                    @elseif(auth()->user()->isTenantUser())
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
                    @elseif(auth()->user()->isStaff())
                        <a href="{{ route('tenant.staff-profile') }}" class="rr-nav-link max-w-[7rem] truncate rounded-lg border border-transparent px-2.5 py-1.5 text-xs text-slate-300 hover:border-slate-600 hover:bg-slate-800/50 sm:max-w-[10rem] sm:px-3 sm:text-sm" title="Profile">Profile</a>
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
                $navActive = 'bg-white text-slate-900 border border-slate-200 shadow-sm';
                $navIdle = 'text-slate-600 hover:text-slate-900 hover:bg-slate-100 border border-transparent';
            @endphp
            <a href="{{ route('superadmin.dashboard') }}" class="rr-nav-pill rr-nav-link shrink-0 whitespace-nowrap rounded-full px-3.5 py-2 text-sm font-medium {{ request()->routeIs('superadmin.dashboard') ? $navActive : $navIdle }}">Dashboard</a>
            <a href="{{ route('superadmin.tenants.index') }}" class="rr-nav-pill relative inline-flex shrink-0 items-center whitespace-nowrap rounded-full px-3.5 py-2 text-sm font-medium {{ request()->routeIs('superadmin.tenants.*') ? $navActive : $navIdle }}">
                Tenants
                @if($pendingTenantAppsCount > 0)
                    <span class="rr-nav-badge absolute -right-1 -top-1 h-2.5 w-2.5 rounded-full bg-rose-500 ring-2 ring-white" title="Pending applications"></span>
                @endif
            </a>
            <a href="{{ route('superadmin.plans.index') }}" class="shrink-0 whitespace-nowrap rounded-full px-3.5 py-2 text-sm font-medium {{ request()->routeIs('superadmin.plans.*') ? $navActive : $navIdle }}">Plans</a>
            <a href="{{ route('superadmin.extensions.index') }}" class="rr-nav-pill relative inline-flex shrink-0 items-center whitespace-nowrap rounded-full px-3.5 py-2 text-sm font-medium {{ request()->routeIs('superadmin.extensions.*') ? $navActive : $navIdle }}">
                Extensions
                @if($pendingExtCount > 0)
                    <span class="rr-nav-badge absolute -right-1 -top-1 h-2.5 w-2.5 rounded-full bg-rose-500 ring-2 ring-white"></span>
                @endif
            </a>
            <a href="{{ route('superadmin.about') }}" class="shrink-0 whitespace-nowrap rounded-full px-3.5 py-2 text-sm font-medium {{ request()->routeIs('superadmin.about', 'superadmin.support') ? $navActive : $navIdle }}">Support</a>
        @elseif(auth()->user()->isTenantUser())
            @php
                $tid = auth()->user()->tenant_id;
                $tenantUser = auth()->user();
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
                $hasSalesDashboard = $tn?->hasFeature(\App\Models\Tenant::FEATURE_SALES_DASHBOARD);
                $hasAnalytics = $tn?->hasFeature(\App\Models\Tenant::FEATURE_ADVANCED_ANALYTICS);
                $hasPay = $tn?->hasFeature(\App\Models\Tenant::FEATURE_PAYMENT_TRACKING);
                $canVehicles = $tenantUser->hasPermission('vehicles.manage');
                $canMaint = $tenantUser->hasPermission('maintenance.manage');
                $canCustomers = $tenantUser->hasPermission('customers.manage');
                $canReports = $tenantUser->hasPermission('reports.view');
                $canBookings = $tenantUser->hasPermission('bookings.manage');
                $canPayments = $tenantUser->hasPermission('payments.manage');
                $canStaff = $tenantUser->canManageStaff();
                $navActive = 'bg-white text-slate-900 border border-slate-200 shadow-sm';
                $navIdle = 'text-slate-600 hover:text-slate-900 hover:bg-slate-100 border border-transparent';
            @endphp
            <a href="{{ route('admin.dashboard') }}" class="rr-nav-pill relative inline-flex shrink-0 items-center whitespace-nowrap rounded-full px-3.5 py-2 text-sm font-medium {{ request()->routeIs('admin.dashboard') ? $navActive : $navIdle }}">
                Dashboard
                @if($tenantDashboardPending > 0)
                    <span class="rr-nav-badge absolute -right-1 -top-1 h-2.5 w-2.5 rounded-full bg-rose-500 ring-2 ring-white" title="Needs attention"></span>
                @endif
            </a>
            @if($canVehicles)
                <a href="{{ route('vehicles.index') }}" class="shrink-0 whitespace-nowrap rounded-full px-3.5 py-2 text-sm font-medium {{ request()->routeIs('vehicles.*') && !request()->routeIs('vehicles.maintenance') ? $navActive : $navIdle }}">Vehicles</a>
            @endif
            @if($hasMaint && $canMaint)
                <a href="{{ route('vehicles.maintenance') }}" class="shrink-0 whitespace-nowrap rounded-full px-3.5 py-2 text-sm font-medium {{ request()->routeIs('vehicles.maintenance') ? $navActive : $navIdle }}">Maintenance</a>
            @endif
            @if($canCustomers)
                <a href="{{ route('customers.index') }}" class="shrink-0 whitespace-nowrap rounded-full px-3.5 py-2 text-sm font-medium {{ request()->routeIs('customers.*') ? $navActive : $navIdle }}">Customers</a>
            @endif
            @if($hasSalesDashboard && $canReports)
                <a href="{{ route('tenant.reports') }}" class="shrink-0 whitespace-nowrap rounded-full px-3.5 py-2 text-sm font-medium {{ request()->routeIs('tenant.reports') ? $navActive : $navIdle }}">Reports</a>
            @endif
            @if($hasAnalytics && $canReports)
                <a href="{{ route('tenant.analytics') }}" class="shrink-0 whitespace-nowrap rounded-full px-3.5 py-2 text-sm font-medium {{ request()->routeIs('tenant.analytics') ? $navActive : $navIdle }}">Analytics</a>
            @endif
            @if($canBookings)
                <a href="{{ route('bookings.index') }}" class="rr-nav-pill inline-flex shrink-0 items-center gap-1.5 whitespace-nowrap rounded-full px-3.5 py-2 text-sm font-medium {{ request()->routeIs('bookings.index', 'bookings.create', 'bookings.store', 'bookings.updateStatus') ? $navActive : $navIdle }}">
                    <span>Bookings</span>
                    @if($tenantPendingBookingsCount > 0)
                        <span class="rr-nav-badge ml-1 inline-flex h-[18px] min-w-[18px] shrink-0 items-center justify-center rounded-full bg-rose-500 px-1 text-[10px] font-bold leading-none text-white ring-2 ring-white">{{ $tenantPendingBookingsCount > 9 ? '9+' : $tenantPendingBookingsCount }}</span>
                    @endif
                </a>
            @endif
            @if($hasCal && $canBookings)
                <a href="{{ route('bookings.calendar') }}" class="shrink-0 whitespace-nowrap rounded-full px-3.5 py-2 text-sm font-medium {{ request()->routeIs('bookings.calendar') ? $navActive : $navIdle }}">Calendar</a>
            @endif
            @if($hasPay && $canPayments)
                <a href="{{ route('payments.index') }}" class="shrink-0 whitespace-nowrap rounded-full px-3.5 py-2 text-sm font-medium {{ request()->routeIs('payments.*') ? $navActive : $navIdle }}">Payments</a>
            @endif
            @if($canStaff)
                <a href="{{ route('admin.staff.index') }}" class="shrink-0 whitespace-nowrap rounded-full px-3.5 py-2 text-sm font-medium {{ request()->routeIs('admin.staff.*') ? $navActive : $navIdle }}">Staff</a>
            @endif
            <a href="{{ route('admin.about') }}" class="shrink-0 whitespace-nowrap rounded-full px-3.5 py-2 text-sm font-medium {{ request()->routeIs('admin.about', 'admin.support') ? $navActive : $navIdle }}">About</a>
        @else
            @php
                $navActive = 'bg-white text-slate-900 border border-slate-200 shadow-sm';
                $navIdle = 'text-slate-600 hover:text-slate-900 hover:bg-slate-100 border border-transparent';
            @endphp
            <a href="{{ route('customer.dashboard') }}" class="shrink-0 whitespace-nowrap rounded-full px-3.5 py-2 text-sm font-medium {{ request()->routeIs('customer.dashboard') ? $navActive : $navIdle }}">Dashboard</a>
            <a href="{{ route('customer.search') }}" class="shrink-0 whitespace-nowrap rounded-full px-3.5 py-2 text-sm font-medium {{ request()->routeIs('customer.search') ? $navActive : $navIdle }}">Search cars</a>
            <a href="{{ route('customer.tenants.index') }}" class="shrink-0 whitespace-nowrap rounded-full px-3.5 py-2 text-sm font-medium {{ request()->routeIs('customer.tenants.*') ? $navActive : $navIdle }}">Companies</a>
        @endif
            </div>
        </div>
    </div>
</nav>
@endif
@endauth
</div>

<main class="mx-auto max-w-6xl px-4 pb-16 pt-8 sm:px-6 lg:max-w-7xl leading-relaxed">
    @yield('content')
</main>

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
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (!window.Swal) return;

        document.querySelectorAll('form[data-confirm]').forEach((form) => {
            form.addEventListener('submit', function onConfirmSubmit(e) {
                e.preventDefault();
                Swal.fire({
                    icon: form.dataset.confirmIcon || 'warning',
                    title: form.dataset.confirmTitle || 'Are you sure?',
                    text: form.dataset.confirmText || 'Please confirm this action.',
                    showCancelButton: true,
                    confirmButtonText: form.dataset.confirmButton || 'Yes, continue',
                    cancelButtonText: form.dataset.cancelButton || 'Cancel',
                    confirmButtonColor: form.dataset.confirmColor || '#dc2626',
                }).then((result) => {
                    if (!result.isConfirmed) {
                        return;
                    }
                    /* Remove handler so submit() is a real native submit (includes _token). Re-attaching dataset + submit() can 419 in some browsers. */
                    form.removeEventListener('submit', onConfirmSubmit);
                    form.submit();
                });
            });
        });
    });
</script>
@stack('scripts')
</body>
</html>

