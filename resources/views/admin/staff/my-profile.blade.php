@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="mx-auto max-w-xl">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold">My profile</h1>
        <p class="text-sm text-slate-500">Update your name, email, and password. Company settings are managed by an admin.</p>
    </div>

    <div class="rr-panel-elevated p-6">
        <form method="POST" action="{{ route('tenant.staff-profile.update') }}" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="rr-label" for="name">Name</label>
                <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required class="rr-input">
            </div>

            <div>
                <label class="rr-label" for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required class="rr-input">
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="rr-label" for="password">New password (optional)</label>
                    <input id="password" type="password" name="password" class="rr-input" autocomplete="new-password">
                </div>
                <div>
                    <label class="rr-label" for="password_confirmation">Confirm new password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" class="rr-input" autocomplete="new-password">
                </div>
            </div>

            <div class="flex flex-wrap gap-3">
                <button type="submit" class="rr-btn-primary inline-flex items-center rounded-lg px-5 py-2.5 text-sm font-semibold">Save</button>
                <a href="{{ route('admin.dashboard') }}" class="rr-btn-secondary inline-flex items-center rounded-lg px-5 py-2.5 text-sm font-semibold">Back to dashboard</a>
            </div>
        </form>
    </div>
</div>
@endsection
