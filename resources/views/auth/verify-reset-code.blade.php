@extends('layouts.app')

@section('title', 'Enter reset code - RentRide')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center py-10">
    <div class="w-full max-w-md rr-panel-elevated p-8">
        <h2 class="text-2xl font-semibold text-center mb-2 text-slate-900">Verification code</h2>
        <p class="text-center text-sm text-slate-600 mb-1">Code sent to</p>
        <p class="text-center text-sm font-medium text-slate-900 mb-6">{{ $email }}</p>

        <form method="POST" action="{{ url('/reset-password/verify') }}" class="space-y-4">
            @csrf
            <div>
                <label class="rr-label" for="vrc-code">Code</label>
                <input
                    id="vrc-code"
                    type="text"
                    name="code"
                    inputmode="numeric"
                    maxlength="6"
                    autocomplete="one-time-code"
                    value="{{ old('code') }}"
                    required
                    autofocus
                    class="rr-input tracking-[0.35em] text-center text-lg"
                >
            </div>
            <button type="submit" class="rr-btn-primary w-full inline-flex justify-center items-center rounded-lg py-2.5 text-sm font-semibold shadow-lg transition">
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
