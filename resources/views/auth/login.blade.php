@extends('layouts.app')

@section('title', 'Login - RentRide')

@section('content')
<div class="min-h-[70vh] flex flex-col items-center justify-start gap-10 py-8">
    <div class="w-full max-w-md bg-slate-800/70 border border-slate-700 rounded-2xl shadow-2xl p-8 backdrop-blur">
        <h2 class="text-2xl font-semibold text-center mb-6">Super Admin Login</h2>
        <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium mb-1">Email</label>
                <input
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    class="w-full rounded-lg border border-slate-600 bg-slate-900/70 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                >
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Password</label>
                <input
                    type="password"
                    name="password"
                    required
                    class="w-full rounded-lg border border-slate-600 bg-slate-900/70 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                >
            </div>
            <div class="flex items-center justify-between text-sm">
                <label class="inline-flex items-center gap-2">
                    <input type="checkbox" name="remember" id="remember" class="rounded border-slate-600 bg-slate-900 text-emerald-500 focus:ring-emerald-500">
                    <span>Remember me</span>
                </label>
            </div>
            <button
                class="w-full inline-flex justify-center items-center rounded-lg bg-emerald-500 py-2.5 text-sm font-semibold text-white shadow-lg shadow-emerald-500/30 hover:bg-emerald-600 transition"
            >
                Login
            </button>
        </form>
    </div>

    <div class="w-full max-w-5xl">
        <h3 class="text-center text-xl font-semibold mb-6">Choose your rental company subscription</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <a href="{{ route('tenant.register', ['plan' => 'basic']) }}" class="group">
                <div class="h-full rounded-2xl border border-sky-500/60 bg-slate-800/70 p-6 shadow-xl group-hover:border-sky-400 group-hover:-translate-y-1 transition transform">
                    <h4 class="text-center text-lg font-semibold text-sky-400 mb-1">Basic</h4>
                    <p class="text-center text-2xl font-bold mb-4">₱249 <span class="text-sm font-normal text-slate-300">/ month</span></p>
                    <ul class="space-y-1 text-sm mb-4">
                        <li>• Core booking management</li>
                        <li>• Vehicle listing</li>
                        <li>• Customer records</li>
                    </ul>
                    <button class="mt-auto w-full rounded-lg border border-sky-500 text-sky-300 py-2 text-sm font-semibold group-hover:bg-sky-500 group-hover:text-slate-900 transition">
                        Choose Basic
                    </button>
                </div>
            </a>

            <a href="{{ route('tenant.register', ['plan' => 'standard']) }}" class="group">
                <div class="h-full rounded-2xl border border-emerald-500/70 bg-slate-800/80 p-6 shadow-2xl group-hover:border-emerald-400 group-hover:-translate-y-1.5 transition transform">
                    <div class="flex justify-center mb-2">
                        <span class="inline-flex items-center rounded-full bg-emerald-500/10 px-3 py-0.5 text-xs font-semibold text-emerald-300">
                            Most Popular
                        </span>
                    </div>
                    <h4 class="text-center text-lg font-semibold text-emerald-400 mb-1">Standard</h4>
                    <p class="text-center text-2xl font-bold mb-4">₱449 <span class="text-sm font-normal text-slate-300">/ month</span></p>
                    <ul class="space-y-1 text-sm mb-4">
                        <li>• Everything in Basic</li>
                        <li>• Payment tracking</li>
                        <li>• Sales dashboard</li>
                    </ul>
                    <button class="mt-auto w-full rounded-lg bg-emerald-500 text-slate-900 py-2 text-sm font-semibold shadow-lg shadow-emerald-500/40 group-hover:bg-emerald-400 transition">
                        Choose Standard
                    </button>
                </div>
            </a>

            <a href="{{ route('tenant.register', ['plan' => 'premium']) }}" class="group">
                <div class="h-full rounded-2xl border border-amber-400/70 bg-slate-800/70 p-6 shadow-xl group-hover:border-amber-300 group-hover:-translate-y-1 transition transform">
                    <h4 class="text-center text-lg font-semibold text-amber-300 mb-1">Premium</h4>
                    <p class="text-center text-2xl font-bold mb-4">₱699 <span class="text-sm font-normal text-slate-300">/ month</span></p>
                    <ul class="space-y-1 text-sm mb-4">
                        <li>• Everything in Standard</li>
                        <li>• Advanced analytics</li>
                        <li>• Maintenance tracking</li>
                        <li>• Auto notifications</li>
                        <li>• Featured listings</li>
                    </ul>
                    <button class="mt-auto w-full rounded-lg border border-amber-400 text-amber-200 py-2 text-sm font-semibold group-hover:bg-amber-400 group-hover:text-slate-900 transition">
                        Choose Premium
                    </button>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection
