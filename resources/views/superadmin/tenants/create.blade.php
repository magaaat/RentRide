@extends('layouts.app')

@section('title', 'Add tenant')

@section('content')
<div class="mx-auto w-full max-w-3xl">
    <div class="mb-8 text-center sm:text-left">
        <h1 class="text-2xl font-semibold tracking-tight text-slate-50 sm:text-3xl">Add tenant</h1>
        <p class="mt-2 text-sm text-slate-400">New tenant and admin account</p>
    </div>

    <div class="rr-panel-elevated p-6 sm:p-8">
        <form method="POST" action="{{ route('superadmin.tenants.store') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <div>
                    <label class="rr-label" for="company_name">Company name</label>
                    <input id="company_name" type="text" name="company_name" value="{{ old('company_name') }}" required class="rr-input">
                </div>
                <div>
                    <label class="rr-label" for="owner_name">Owner name</label>
                    <input id="owner_name" type="text" name="owner_name" value="{{ old('owner_name') }}" required class="rr-input">
                </div>
            </div>

            <div>
                <label class="rr-label" for="email">Admin email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="off" class="rr-input">
            </div>

            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <div>
                    <label class="rr-label" for="phone">Phone</label>
                    <input id="phone" type="text" name="phone" value="{{ old('phone') }}" required class="rr-input">
                </div>
                <div>
                    <label class="rr-label" for="address">Address</label>
                    <input id="address" type="text" name="address" value="{{ old('address') }}" class="rr-input">
                </div>
            </div>

            <div>
                <label class="rr-label" for="subscription_plan">Plan</label>
                <select id="subscription_plan" name="subscription_plan" required class="rr-input">
                    @foreach($plans as $plan)
                        <option value="{{ $plan->key }}" @selected(old('subscription_plan', 'basic') === $plan->key)>
                            {{ $plan->name }} ({{ $plan->key }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="rr-label" for="domain">Domain <span class="font-normal text-slate-500">(optional)</span></label>
                <input id="domain" type="text" name="domain" value="{{ old('domain') }}" placeholder="e.g. company.localhost" class="rr-input">
                <p class="mt-1.5 text-xs text-slate-500">Leave blank to auto-generate: company-name.localhost (port is added from APP_URL, e.g. :8000).</p>
            </div>

            <div class="rounded-xl border border-slate-700/80 bg-slate-900/50 p-4 space-y-4">
                <h3 class="text-sm font-semibold text-violet-300">Manual payment details</h3>
                <p class="text-xs text-slate-400">Provide payment information and upload proof for internal verification (no payment API).</p>
                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    <div>
                        <label class="rr-label" for="payment_method">Payment method</label>
                        <select id="payment_method" name="payment_method" required class="rr-input">
                            <option value="">Select method</option>
                            <option value="gcash" @selected(old('payment_method') === 'gcash')>GCash</option>
                            <option value="maya" @selected(old('payment_method') === 'maya')>Maya</option>
                            <option value="bank_transfer" @selected(old('payment_method') === 'bank_transfer')>Bank Transfer</option>
                            <option value="cash" @selected(old('payment_method') === 'cash')>Cash</option>
                        </select>
                    </div>
                    <div>
                        <label class="rr-label" for="payment_reference">Reference number</label>
                        <input id="payment_reference" type="text" name="payment_reference" value="{{ old('payment_reference') }}" required class="rr-input" placeholder="Transaction or receipt reference">
                    </div>
                </div>
                <div>
                    <label class="rr-label" for="payment_proof">Proof of payment (JPG, PNG, PDF)</label>
                    <input id="payment_proof" type="file" name="payment_proof" accept=".jpg,.jpeg,.png,.pdf" required class="rr-input">
                </div>
                <div>
                    <label class="rr-label" for="payment_notes">Payment notes (optional)</label>
                    <textarea id="payment_notes" name="payment_notes" rows="2" class="rr-input" placeholder="Additional payment verification notes">{{ old('payment_notes') }}</textarea>
                </div>
            </div>

            <div class="flex flex-wrap justify-end gap-3 pt-2">
                <a href="{{ route('superadmin.tenants.index') }}" class="inline-flex items-center rounded-lg rr-btn-secondary px-5 py-2.5 text-sm font-semibold">Cancel</a>
                <button type="submit" class="rr-btn-primary inline-flex items-center rounded-lg px-5 py-2.5 text-sm font-semibold shadow-sm transition">Create tenant</button>
            </div>
        </form>
    </div>
</div>
@endsection
