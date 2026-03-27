@extends('layouts.app')

@section('title', 'Add tenant')

@section('content')
<div class="mx-auto flex w-full max-w-3xl flex-col items-center">
<div class="mb-6 w-full text-center">
    <h3 class="text-xl font-semibold">Add tenant</h3>
</div>

<div class="w-full rounded-xl border border-slate-800 bg-slate-900/60 p-6">
    <p class="mb-6 text-sm text-slate-400">Creates an approved rental company and admin account. A temporary password will be generated and sent by email.</p>

    <form method="POST" action="{{ route('superadmin.tenants.store') }}" class="space-y-5">
        @csrf
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div>
                <label class="mb-1 block text-sm font-medium">Company name</label>
                <input type="text" name="company_name" value="{{ old('company_name') }}" required
                    class="w-full rounded-lg border border-slate-600 bg-slate-950/50 px-3 py-2 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium">Owner name</label>
                <input type="text" name="owner_name" value="{{ old('owner_name') }}" required
                    class="w-full rounded-lg border border-slate-600 bg-slate-950/50 px-3 py-2 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
        </div>

        <div>
            <label class="mb-1 block text-sm font-medium">Admin email</label>
            <input type="email" name="email" value="{{ old('email') }}" required autocomplete="off"
                class="w-full rounded-lg border border-slate-600 bg-slate-950/50 px-3 py-2 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-emerald-500">
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div>
                <label class="mb-1 block text-sm font-medium">Phone</label>
                <input type="text" name="phone" value="{{ old('phone') }}" required
                    class="w-full rounded-lg border border-slate-600 bg-slate-950/50 px-3 py-2 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium">Address</label>
                <input type="text" name="address" value="{{ old('address') }}"
                    class="w-full rounded-lg border border-slate-600 bg-slate-950/50 px-3 py-2 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
        </div>

        <div>
            <label class="mb-1 block text-sm font-medium">Plan</label>
            <select name="subscription_plan" required
                class="w-full rounded-lg border border-slate-600 bg-slate-950/50 px-3 py-2 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                @foreach($plans as $plan)
                    <option value="{{ $plan->key }}" @selected(old('subscription_plan', 'basic') === $plan->key)>
                        {{ $plan->name }} ({{ $plan->key }})
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="mb-1 block text-sm font-medium">Domain <span class="font-normal text-slate-500">(optional)</span></label>
            <input type="text" name="domain" value="{{ old('domain') }}" placeholder="e.g. company.rentride.test"
                class="w-full rounded-lg border border-slate-600 bg-slate-950/50 px-3 py-2 text-sm text-slate-100 placeholder:text-slate-600 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            <p class="mt-1 text-xs text-slate-500">Leave blank to use auto: tenant{id}.rentride.test</p>
        </div>

        <div class="flex justify-end gap-2 pt-2">
            <a href="{{ route('superadmin.tenants.index') }}" class="inline-flex items-center rounded-lg border border-slate-600 px-4 py-2 text-sm font-medium text-slate-300 hover:bg-slate-800">Cancel</a>
            <button type="submit" class="inline-flex items-center rounded-lg bg-emerald-500 px-4 py-2 text-sm font-semibold text-slate-950 hover:bg-emerald-400">
                Create tenant
            </button>
        </div>
    </form>
</div>
</div>
@endsection
