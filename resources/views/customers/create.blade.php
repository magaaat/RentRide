@extends('layouts.app')

@section('title', 'Add customer')

@section('content')
<div class="mx-auto max-w-3xl">
    <div class="mb-8">
        <h1 class="text-2xl font-semibold tracking-tight text-slate-50">Add customer</h1>
        <p class="mt-1 text-sm text-slate-400">Create a customer record for your rental company.</p>
    </div>

    <div class="rr-panel-elevated p-6 sm:p-8">
        <form method="POST" action="{{ route('customers.store') }}" class="space-y-5">
            @csrf
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                    <label class="rr-label" for="name">Name</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required class="rr-input">
                </div>
                <div>
                    <label class="rr-label" for="email">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" class="rr-input">
                </div>
                <div>
                    <label class="rr-label" for="phone">Phone</label>
                    <input id="phone" type="text" name="phone" value="{{ old('phone') }}" class="rr-input">
                </div>
                <div>
                    <label class="rr-label" for="address">Address</label>
                    <input id="address" type="text" name="address" value="{{ old('address') }}" class="rr-input">
                </div>
            </div>
            <div class="flex flex-wrap gap-3 pt-2">
                <button type="submit" class="inline-flex items-center rounded-lg rr-btn-primary px-5 py-2.5 text-sm font-semibold shadow-sm">Save</button>
                <a href="{{ route('customers.index') }}" class="inline-flex items-center rounded-lg rr-btn-secondary px-5 py-2.5 text-sm font-semibold">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
