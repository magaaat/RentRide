@extends('layouts.app')

@section('title', 'Customer Login - RentRide')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center py-10">
    <div class="w-full max-w-md bg-slate-800/70 border border-slate-700 rounded-2xl shadow-2xl p-8 backdrop-blur">
        <h2 class="text-2xl font-semibold text-center mb-2">Rent a car</h2>
        <p class="text-center text-sm text-slate-400 mb-6">Sign in to browse rental companies and manage your bookings.</p>
        <form method="POST" action="{{ route('customer.login.post') }}" class="space-y-4" autocomplete="off">
            @csrf
            <div>
                <label class="block text-sm font-medium mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                    class="w-full rounded-lg border border-slate-600 bg-slate-900/70 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Password</label>
                <input type="password" name="password" required autocomplete="current-password"
                    class="w-full rounded-lg border border-slate-600 bg-slate-900/70 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
            </div>
            <div class="flex items-center justify-between text-sm">
                <label class="inline-flex items-center gap-2">
                    <input type="checkbox" name="remember" class="rounded border-slate-600 bg-slate-900 text-emerald-500 focus:ring-emerald-500">
                    <span>Remember me</span>
                </label>
                <a href="{{ url('/forgot-password?from=customer') }}" class="text-emerald-400 hover:text-emerald-300">Forgot password?</a>
            </div>
            <button type="submit" class="w-full inline-flex justify-center items-center rounded-lg bg-emerald-500 py-2.5 text-sm font-semibold text-slate-950 shadow-lg shadow-emerald-500/30 hover:bg-emerald-400 transition">
                Sign in
            </button>
        </form>
        <p class="mt-6 text-center text-sm text-slate-400">
            No account?
            <a href="{{ route('customer.register') }}" class="text-emerald-400 hover:text-emerald-300 font-semibold">Create one</a>
        </p>
        <p class="mt-2 text-center text-sm">
            <a href="{{ url('/') }}" class="text-slate-400 hover:text-slate-200 transition">Back to home</a>
        </p>
    </div>
</div>
@endsection
