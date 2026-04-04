@extends('layouts.app')

@section('title', 'Login - RentRide')

@section('content')
<div class="min-h-[70vh] flex flex-col items-center justify-start gap-8 py-6 sm:gap-10 sm:py-8">
    <div class="w-full max-w-md rr-panel-elevated p-5 sm:p-8">
        <h2 class="mb-5 text-center text-xl font-semibold text-slate-900 sm:mb-6 sm:text-2xl">Super Admin Login</h2>
        <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
            @csrf
            <div>
                <label class="rr-label" for="login-email">Email</label>
                <input
                    id="login-email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    class="rr-input"
                >
            </div>
            <div>
                <label class="rr-label" for="login-password">Password</label>
                <input
                    id="login-password"
                    type="password"
                    name="password"
                    required
                    class="rr-input"
                >
            </div>
            <div class="flex items-center justify-between text-sm">
                <label class="inline-flex items-center gap-2 text-slate-600">
                    <input type="checkbox" name="remember" id="remember" class="rounded border-slate-300 text-sky-500 focus:ring-sky-500">
                    <span>Remember me</span>
                </label>
            </div>
            <button type="submit" class="rr-btn-primary w-full inline-flex justify-center items-center rounded-lg py-2.5 text-sm font-semibold shadow-lg transition">
                Login
            </button>
        </form>
    </div>

    <div class="w-full max-w-6xl">
        <h3 class="mb-5 text-center text-lg font-semibold text-slate-900 sm:mb-6 sm:text-xl">Choose your rental company subscription</h3>
        <div class="grid grid-cols-1 gap-4 sm:gap-6 lg:grid-cols-3">
            <a href="{{ route('tenant.register', ['plan' => 'basic']) }}" class="group">
                <div class="h-full rounded-2xl border border-sky-200 bg-gradient-to-b from-white to-sky-50/80 p-5 shadow-rr transition transform group-hover:border-sky-300 group-hover:-translate-y-1 sm:p-6">
                    <h4 class="text-center text-lg font-semibold text-sky-700 mb-1">Basic</h4>
                    <p class="text-center text-2xl font-bold text-slate-900 mb-4">₱249 <span class="text-sm font-normal text-slate-500">/ month</span></p>
                    <ul class="space-y-1 text-sm mb-4 text-slate-600">
                        <li>• Core booking management</li>
                        <li>• Vehicle listing</li>
                        <li>• Customer records</li>
                    </ul>
                    <span class="mt-auto flex w-full justify-center rounded-lg border border-sky-400 bg-white py-2 text-sm font-semibold text-sky-700 transition group-hover:bg-sky-500 group-hover:text-white">
                        Choose Basic
                    </span>
                </div>
            </a>

            <a href="{{ route('tenant.register', ['plan' => 'standard']) }}" class="group">
                <div class="h-full rounded-2xl border border-emerald-300 bg-gradient-to-b from-white to-emerald-50/90 p-5 shadow-rr transition transform group-hover:border-emerald-400 group-hover:-translate-y-1.5 sm:p-6">
                    <div class="flex justify-center mb-2">
                        <span class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-0.5 text-xs font-semibold text-emerald-800">
                            Most Popular
                        </span>
                    </div>
                    <h4 class="text-center text-lg font-semibold text-emerald-800 mb-1">Standard</h4>
                    <p class="text-center text-2xl font-bold text-slate-900 mb-4">₱449 <span class="text-sm font-normal text-slate-500">/ month</span></p>
                    <ul class="space-y-1 text-sm mb-4 text-slate-600">
                        <li>• Everything in Basic</li>
                        <li>• Payment tracking</li>
                        <li>• Sales dashboard</li>
                    </ul>
                    <span class="mt-auto flex w-full justify-center rounded-lg bg-emerald-600 py-2 text-sm font-semibold text-white shadow-md shadow-emerald-600/25 transition group-hover:bg-emerald-500">
                        Choose Standard
                    </span>
                </div>
            </a>

            <a href="{{ route('tenant.register', ['plan' => 'premium']) }}" class="group">
                <div class="h-full rounded-2xl border border-amber-200 bg-gradient-to-b from-white to-amber-50/80 p-5 shadow-rr transition transform group-hover:border-amber-300 group-hover:-translate-y-1 sm:p-6">
                    <h4 class="text-center text-lg font-semibold text-amber-800 mb-1">Premium</h4>
                    <p class="text-center text-2xl font-bold text-slate-900 mb-4">₱699 <span class="text-sm font-normal text-slate-500">/ month</span></p>
                    <ul class="space-y-1 text-sm mb-4 text-slate-600">
                        <li>• Everything in Standard</li>
                        <li>• Advanced analytics</li>
                        <li>• Maintenance tracking</li>
                        <li>• Auto notifications</li>
                        <li>• Featured listings</li>
                    </ul>
                    <span class="mt-auto flex w-full justify-center rounded-lg border border-amber-400 bg-amber-50 py-2 text-sm font-semibold text-amber-900 transition group-hover:bg-amber-400 group-hover:text-slate-900">
                        Choose Premium
                    </span>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection
