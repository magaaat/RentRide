@extends('layouts.app')

@section('title', 'Forgot Password - RentRide')

@section('content')
@php
    $pr = session('password_reset_return', []);
@endphp
<div class="min-h-[70vh] flex items-center justify-center py-10">
    <div class="w-full max-w-md bg-slate-800/70 border border-slate-700 rounded-2xl shadow-2xl p-8 backdrop-blur">
        <h2 class="text-2xl font-semibold text-center mb-2">Forgot password</h2>
        <p class="text-center text-sm text-slate-400 mb-6">We’ll email you a reset code.</p>

        <form method="POST" action="{{ url('/forgot-password') }}" class="space-y-4">
            @csrf
            @if(!empty($pr['from']))
                <input type="hidden" name="from" value="{{ $pr['from'] }}">
            @endif
            @if(isset($pr['tenant']) && $pr['tenant'] !== null && $pr['tenant'] !== '')
                <input type="hidden" name="tenant" value="{{ $pr['tenant'] }}">
            @endif
            <div>
                <label class="block text-sm font-medium mb-1">Email</label>
                <input
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autocomplete="email"
                    class="w-full rounded-lg border border-slate-600 bg-slate-900/70 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                >
            </div>
            <button type="submit" class="w-full inline-flex justify-center items-center rounded-lg bg-emerald-500 py-2.5 text-sm font-semibold text-slate-950 shadow-lg shadow-emerald-500/30 hover:bg-emerald-400 transition">
                Send code
            </button>
        </form>

        @include('auth.partials.password-reset-nav', ['returnUrl' => $returnUrl])
    </div>
</div>
@endsection
