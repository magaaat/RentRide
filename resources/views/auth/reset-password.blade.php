@extends('layouts.app')

@section('title', 'New password - RentRide')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center py-10">
    <div class="w-full max-w-md bg-slate-800/70 border border-slate-700 rounded-2xl shadow-2xl p-8 backdrop-blur">
        <h2 class="text-2xl font-semibold text-center mb-2">Reset password</h2>
        <p class="text-center text-sm text-slate-400 mb-1">Account</p>
        <p class="text-center text-sm font-medium text-slate-200 mb-6">{{ $email }}</p>

        <form method="POST" action="{{ url('/reset-password') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium mb-1">Password</label>
                <input
                    type="password"
                    name="password"
                    required
                    autocomplete="new-password"
                    class="w-full rounded-lg border border-slate-600 bg-slate-900/70 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                >
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Confirm</label>
                <input
                    type="password"
                    name="password_confirmation"
                    required
                    autocomplete="new-password"
                    class="w-full rounded-lg border border-slate-600 bg-slate-900/70 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                >
            </div>
            <button type="submit" class="w-full inline-flex justify-center items-center rounded-lg bg-emerald-500 py-2.5 text-sm font-semibold text-slate-950 shadow-lg shadow-emerald-500/30 hover:bg-emerald-400 transition">
                Save
            </button>
        </form>

        @include('auth.partials.password-reset-nav', [
            'returnUrl' => $returnUrl,
            'secondaryHref' => url('/reset-password/verify'),
            'secondaryLabel' => 'Re-enter verification code',
        ])
    </div>
</div>
@endsection
