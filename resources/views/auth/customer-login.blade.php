@extends('layouts.app')

@section('title', 'Customer Login - RentRide')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center py-10">
    <div class="w-full max-w-md rr-panel-elevated p-8">
        <h2 class="text-2xl font-semibold text-center mb-2 text-slate-900">Rent a car</h2>
        <p class="text-center text-sm text-slate-600 mb-6">Sign in to browse rental companies and manage your bookings.</p>
        <form method="POST" action="{{ route('customer.login.post') }}" class="space-y-4" autocomplete="off">
            @csrf
            <div>
                <label class="rr-label" for="cust-email">Email</label>
                <input id="cust-email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" class="rr-input">
            </div>
            <div>
                <label class="rr-label" for="cust-password">Password</label>
                <input id="cust-password" type="password" name="password" required autocomplete="current-password" class="rr-input">
            </div>
            <div class="flex items-center justify-between text-sm">
                <label class="inline-flex items-center gap-2 text-slate-600">
                    <input type="checkbox" name="remember" class="rounded border-slate-300 text-sky-500 focus:ring-sky-500">
                    <span>Remember me</span>
                </label>
                <a href="{{ url('/forgot-password?from=customer') }}" class="rr-link-accent font-medium">Forgot password?</a>
            </div>
            <button type="submit" class="rr-btn-primary w-full inline-flex justify-center items-center rounded-lg py-2.5 text-sm font-semibold shadow-lg transition">
                Sign in
            </button>
        </form>
        <p class="mt-6 text-center text-sm text-slate-600">
            No account?
            <a href="{{ route('customer.register') }}" class="rr-link-accent font-semibold">Create one</a>
        </p>
        <p class="mt-2 text-center text-sm">
            <a href="{{ url('/') }}" class="text-slate-500 hover:text-slate-800 transition">Back to home</a>
        </p>
    </div>
</div>
@endsection
