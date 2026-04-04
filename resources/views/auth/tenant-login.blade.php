@extends('layouts.app')

@section('title', 'Tenant Login - RentRide')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center py-8">
    <div class="w-full max-w-md rr-panel-elevated p-8">
        <h2 class="text-2xl font-semibold text-center mb-6 text-slate-900">Tenant Login</h2>
        @isset($tenant)
            <p class="text-center text-xs text-slate-600 mb-4">
                Signing in for <span class="font-semibold text-slate-900">{{ $tenant->company_name }}</span>
            </p>
        @endisset
        <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
            @csrf
            @isset($tenant)
                <input type="hidden" name="login_tenant_id" value="{{ $tenant->id }}">
            @endisset
            <div>
                <label class="rr-label" for="tl-email">Email</label>
                <input
                    id="tl-email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    class="rr-input"
                >
            </div>
            <div>
                <label class="rr-label" for="tl-password">Password</label>
                <input
                    id="tl-password"
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
                <a href="{{ url('/forgot-password?from=tenant' . (isset($tenant) ? '&tenant=' . ($tenant->slug ?? $tenant->id) : '')) }}" class="rr-link-accent font-medium">Forgot password?</a>
            </div>
            <button type="submit" class="rr-btn-primary w-full inline-flex justify-center items-center rounded-lg py-2.5 text-sm font-semibold shadow-lg transition">
                Login
            </button>
        </form>
    </div>
</div>
@endsection
