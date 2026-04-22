@extends('layouts.app')

@section('title', 'Tenant support')

@section('content')
<div class="mx-auto max-w-6xl">
    <h1 class="text-2xl font-semibold tracking-tight text-slate-900 sm:text-3xl">Tenant support messages</h1>
    <p class="mt-2 text-sm text-slate-600">Review incoming inquiries submitted by tenant users.</p>
    @if(session('status'))
        <div class="mt-4 rounded-lg border border-emerald-300 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            {{ session('status') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mt-4 rounded-lg border border-rose-300 bg-rose-50 px-4 py-3 text-sm text-rose-800">
            {{ session('error') }}
        </div>
    @endif

    <div class="mt-8">
        <div class="rounded-xl border rr-border rr-surface p-6 shadow-rr">
            <div class="mb-4 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-600">
                <span class="font-semibold text-slate-800">Deployed:</span> {{ $config['version'] }}
                @if(($releaseInfo['available'] ?? false) && !empty($releaseInfo['tag_name']))
                    <span class="mx-2 text-slate-400">•</span>
                    <span class="font-semibold text-slate-800">Latest:</span>
                    <a href="{{ $releaseInfo['html_url'] ?: rtrim((string) $config['github_repo_url'], '/') . '/releases' }}" target="_blank" rel="noopener noreferrer" class="font-semibold text-violet-700 hover:text-violet-600">
                        {{ $releaseInfo['tag_name'] }}
                    </a>
                    @if($versionStatus === 'up_to_date')
                        <span class="mx-2 text-slate-400">•</span><span class="font-semibold text-emerald-700">Up to date</span>
                    @elseif($versionStatus === 'behind')
                        <span class="mx-2 text-slate-400">•</span><span class="font-semibold text-amber-700">Update available</span>
                    @elseif($versionStatus === 'ahead')
                        <span class="mx-2 text-slate-400">•</span><span class="font-semibold text-sky-700">Ahead of latest</span>
                    @endif
                @else
                    <span class="mx-2 text-slate-400">•</span><span class="text-slate-500">Latest release unavailable</span>
                @endif
            </div>
            <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-700">Tenant conversations</h2>
            <div class="mt-3 grid grid-cols-1 gap-4 lg:grid-cols-3">
                <div class="rounded-lg border border-slate-200 bg-slate-50 p-3 lg:col-span-1">
                    <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-500">Tenants</p>
                    <div class="mt-2 max-h-[420px] space-y-2 overflow-y-auto">
                        @forelse($tenants as $tenant)
                            <a
                                href="{{ route('superadmin.about', ['tenant_id' => $tenant->id]) }}"
                                class="block rounded-lg border px-3 py-2 text-sm transition {{ (int) $selectedTenantId === (int) $tenant->id ? 'border-violet-300 bg-violet-100 text-violet-900' : 'border-slate-200 bg-white text-slate-700 hover:border-slate-300' }}">
                                <p class="font-semibold">{{ $tenant->company_name }}</p>
                                <p class="mt-0.5 text-xs text-slate-500">{{ $tenant->domain ?: 'No domain' }}</p>
                            </a>
                        @empty
                            <p class="text-sm text-slate-500">No tenants available.</p>
                        @endforelse
                    </div>
                </div>

                <div class="rounded-lg border border-slate-200 bg-slate-50 p-3 lg:col-span-2">
                    @php
                        $selectedTenant = $tenants->firstWhere('id', $selectedTenantId);
                    @endphp

                    @if($selectedTenant)
                        <div class="mb-2 flex items-center justify-between gap-2">
                            <p class="text-sm font-semibold text-slate-800">{{ $selectedTenant->company_name }}</p>
                            <p class="text-xs text-slate-500">{{ $selectedTenant->domain ?: 'No domain' }}</p>
                        </div>

                        <div id="superadmin-chat-thread" class="max-h-[320px] space-y-3 overflow-y-auto rounded-lg border border-slate-200 bg-white p-3">
                            @forelse($messages as $message)
                                @php
                                    $isAdmin = $message->sender_role === \App\Models\TenantChatMessage::SENDER_SUPER_ADMIN;
                                    $sender = $isAdmin ? 'You' : ($message->user?->name ?: 'Tenant');
                                @endphp
                                <div class="flex {{ $isAdmin ? 'justify-end' : 'justify-start' }}">
                                    <div class="max-w-[88%] rounded-2xl {{ $isAdmin ? 'rounded-br-md border border-violet-300 bg-violet-100 text-violet-900' : 'rounded-bl-md border border-slate-300 bg-white text-slate-900' }} px-3 py-2 text-sm shadow-sm">
                                        <p class="text-[11px] font-semibold {{ $isAdmin ? 'text-violet-700' : 'text-slate-600' }}">{{ $sender }}</p>
                                        <p class="mt-1 whitespace-pre-wrap">{{ $message->message }}</p>
                                        <p class="mt-1 text-[11px] {{ $isAdmin ? 'text-violet-700/80' : 'text-slate-500' }}">{{ $message->created_at?->format('M d, Y h:i A') }}</p>
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-slate-500">No messages yet.</p>
                            @endforelse
                        </div>

                        <form method="POST" action="{{ route('superadmin.about.tenants.messages.store', $selectedTenant) }}" class="mt-3">
                            @csrf
                            <label for="message" class="text-[11px] font-semibold uppercase tracking-wide text-slate-600">Reply</label>
                            <textarea
                                id="message"
                                name="message"
                                rows="3"
                                maxlength="3000"
                                required
                                class="rr-input mt-1"
                                placeholder="Write a reply...">{{ old('message') }}</textarea>
                            @error('message')
                                <p class="mt-2 text-xs text-rose-300">{{ $message }}</p>
                            @enderror
                            <div class="mt-2 flex justify-end">
                                <button type="submit" class="inline-flex items-center rounded-lg rr-btn-primary px-3.5 py-2 text-xs font-semibold">
                                    Send reply
                                </button>
                            </div>
                        </form>
                    @else
                        <p class="text-sm text-slate-500">Select a tenant to open the chat thread.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const thread = document.getElementById('superadmin-chat-thread');
        if (thread) {
            thread.scrollTop = thread.scrollHeight;
        }
    });
</script>
@endpush
