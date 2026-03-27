@extends('layouts.app')

@section('title', 'Tenant Login - RentRide')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center py-8">
    <div class="w-full max-w-md bg-slate-800/70 border border-slate-700 rounded-2xl shadow-2xl p-8 backdrop-blur">
        <h2 class="text-2xl font-semibold text-center mb-6">Tenant Login</h2>
        @isset($tenant)
            <p class="text-center text-xs text-slate-400 mb-4">
                Signing in for <span class="font-semibold text-slate-200">{{ $tenant->company_name }}</span>
            </p>
        @endisset
        <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
            @csrf
            @isset($tenant)
                <input type="hidden" name="login_tenant_id" value="{{ $tenant->id }}">
            @endisset
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
                <a href="{{ url('/forgot-password?from=tenant' . (isset($tenant) ? '&tenant=' . $tenant->id : '')) }}" class="text-emerald-400 hover:text-emerald-300">Forgot password?</a>
            </div>
            <button
                class="w-full inline-flex justify-center items-center rounded-lg bg-emerald-500 py-2.5 text-sm font-semibold text-white shadow-lg shadow-emerald-500/30 hover:bg-emerald-600 transition"
            >
                Login
            </button>
        </form>
    </div>
</div>
@endsection

