@extends('layouts.app')

@section('title', 'Upgrade plan')

@section('content')
<div class="mx-auto max-w-3xl">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold tracking-tight text-slate-50">Upgrade subscription</h1>
        <p class="mt-1 text-sm text-slate-400">Change plan</p>
    </div>

    <div class="rr-panel-elevated p-6 sm:p-8">
        <form method="POST" action="{{ route('subscriptions.upgrade.post') }}" class="space-y-6">
            @csrf
            <div>
                <label class="rr-label" for="plan_key">Plan</label>
                <select id="plan_key" name="plan_key" required class="rr-input">
                    @foreach($plans as $p)
                        <option value="{{ $p->key }}">
                            {{ $p->name }} ({{ $p->key }}) — ₱{{ number_format($p->discountedPrice(), 2) }} / {{ $p->billing_period }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="rr-btn-primary inline-flex items-center rounded-lg px-5 py-2.5 text-sm font-semibold shadow-sm">
                Confirm upgrade
            </button>
        </form>
    </div>
</div>
@endsection
