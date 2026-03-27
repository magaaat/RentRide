@extends('layouts.app')

@section('title', 'Enter reset code - RentRide')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center py-10">
    <div class="w-full max-w-md bg-slate-800/70 border border-slate-700 rounded-2xl shadow-2xl p-8 backdrop-blur">
        <h2 class="text-2xl font-semibold text-center mb-2">Verification code</h2>
        <p class="text-center text-sm text-slate-400 mb-1">Code sent to</p>
        <p class="text-center text-sm font-medium text-slate-200 mb-6">{{ $email }}</p>

        <form method="POST" action="{{ url('/reset-password/verify') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium mb-1">Code</label>
                <input
                    type="text"
                    name="code"
                    inputmode="numeric"
                    maxlength="6"
                    autocomplete="one-time-code"
                    value="{{ old('code') }}"
                    required
                    autofocus
                    class="w-full rounded-lg border border-slate-600 bg-slate-900/70 px-3 py-2 text-sm tracking-[0.35em] text-center text-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                >
            </div>
            <button type="submit" class="w-full inline-flex justify-center items-center rounded-lg bg-emerald-500 py-2.5 text-sm font-semibold text-slate-950 shadow-lg shadow-emerald-500/30 hover:bg-emerald-400 transition">
                Continue
            </button>
        </form>

        @include('auth.partials.password-reset-nav', [
            'returnUrl' => $returnUrl,
            'secondaryHref' => url('/forgot-password'),
            'secondaryLabel' => 'Use a different email',
        ])
    </div>
</div>
@endsection
