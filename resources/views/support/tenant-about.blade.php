@extends('layouts.app')

@section('title', 'About')

@section('content')
<div class="mx-auto max-w-6xl">
    <h1 class="text-2xl font-semibold tracking-tight text-slate-900 sm:text-3xl">About RentRide</h1>
    <p class="mt-2 text-sm text-slate-600">Platform details, current build information, and direct help request to Super Admin.</p>
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

    <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-5">
        <div class="space-y-6 self-start lg:col-span-2">
            <div class="rounded-xl border rr-border rr-surface p-6 shadow-rr">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-600">Current version</h2>
                <p class="mt-1 text-lg font-semibold text-slate-900">{{ $currentVersion }}</p>
                @if(!empty($config['git_sha']))
                    <p class="mt-1 text-xs text-slate-600">Build: {{ $config['git_sha'] }}</p>
                @endif
                @if(($releaseInfo['available'] ?? false) && !empty($releaseInfo['tag_name']))
                    @if($versionStatus === 'behind')
                        <div class="mt-3 rounded-lg border border-amber-300 bg-amber-100 px-3 py-2 text-xs text-amber-900">
                            <p class="font-semibold">New update available</p>
                            <p class="mt-0.5">Your tenant is on {{ $currentVersion }}, while the latest release is {{ $releaseInfo['tag_name'] }}.</p>
                        </div>
                    @endif
                    @if($versionStatus === 'up_to_date')
                        <p class="mt-2 text-xs font-semibold text-emerald-700">Status: Up to date</p>
                    @elseif($versionStatus === 'behind')
                        <p class="mt-2 text-xs font-semibold text-amber-700">
                            <span class="inline-flex h-2 w-2 rounded-full bg-amber-500 align-middle"></span>
                            <span class="ml-1 align-middle">Status: Update available</span>
                        </p>
                    @elseif($versionStatus === 'ahead')
                        <p class="mt-2 text-xs font-semibold text-sky-700">Status: Ahead of latest release</p>
                    @endif
                @endif
                @if(!empty($config['version']) && $config['version'] !== $currentVersion)
                    <p class="mt-2 text-xs text-slate-500">Base platform version: {{ $config['version'] }}</p>
                @endif
                @if(!empty($runtimeAppliedAt))
                    <p class="mt-1 text-xs text-slate-500">Last applied update: {{ \Illuminate\Support\Carbon::parse($runtimeAppliedAt)->format('M d, Y h:i A') }}</p>
                @endif
            </div>

            <div class="rounded-xl border rr-border rr-surface p-6 shadow-rr">
            @php
                $fallbackReleaseUrl = !empty($config['github_repo_url']) ? rtrim((string) $config['github_repo_url'], '/') . '/releases' : '';
            @endphp
                <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-600">Latest version</h2>
                @if(($releaseInfo['available'] ?? false) && !empty($releaseInfo['tag_name']))
                    <p class="mt-1 text-lg font-semibold text-slate-900">{{ $releaseInfo['tag_name'] }}</p>
                    <p class="mt-1 text-xs text-slate-600">
                        Fetched from your latest GitHub release
                        @if(($releaseInfo['is_draft'] ?? false))
                            <span class="font-semibold text-amber-700">(draft)</span>
                        @elseif(($releaseInfo['is_prerelease'] ?? false))
                            <span class="font-semibold text-sky-700">(pre-release)</span>
                        @endif
                        .
                    </p>
                    <p class="mt-2 text-xs text-slate-600">
                        Release page:
                        <a href="{{ $releaseInfo['html_url'] ?: rtrim((string) $config['github_repo_url'], '/') . '/releases' }}" target="_blank" rel="noopener noreferrer" class="font-semibold text-violet-700 hover:text-violet-600">
                            View on GitHub
                        </a>
                    </p>
                @else
                    <p class="mt-2 text-xs text-slate-500">
                        @if(($releaseInfo['reason'] ?? '') === 'missing_repo_url')
                            Latest release check unavailable. Set <code class="rounded bg-slate-100 px-1 py-0.5 text-[11px] text-slate-700">GITHUB_REPO_URL</code>.
                        @elseif(($releaseInfo['reason'] ?? '') === 'connection_failed')
                            Latest release check unavailable due to a network/SSL connection issue.
                        @elseif(($releaseInfo['reason'] ?? '') === 'release_not_found')
                            Latest release check unavailable. No published release is visible; if your release is draft/private, set <code class="rounded bg-slate-100 px-1 py-0.5 text-[11px] text-slate-700">GITHUB_TOKEN</code> with repo access.
                        @elseif(($releaseInfo['reason'] ?? '') === 'auth_failed')
                            Latest release check unavailable. Verify GitHub token permissions/rate limits.
                        @elseif(($releaseInfo['reason'] ?? '') === 'request_failed')
                            Latest release check unavailable. Verify GitHub API access/token.
                        @else
                            Latest release check unavailable.
                        @endif
                    </p>
                @endif
                <div class="mt-4">
                    @if(($releaseInfo['available'] ?? false) && !empty($releaseInfo['tag_name']))
                        <form method="POST" action="{{ route('admin.about.update.download') }}" class="mb-2">
                            @csrf
                            <button
                                type="submit"
                                class="inline-flex items-center rounded-lg rr-btn-primary px-3.5 py-2 text-xs font-semibold">
                                Download update
                            </button>
                        </form>
                        <p class="mt-2 text-xs text-slate-500">Downloads and applies the selected release on this device, then updates tenant access.</p>
                    @endif
                </div>
                @if($latestTenantUpdate)
                    <div class="mt-3 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-700">
                        <span class="font-semibold">Last tenant-applied update:</span>
                        #{{ $latestTenantUpdate->id }} for {{ $latestTenantUpdate->target_version }}
                        <span class="mx-1 text-slate-400">•</span>
                        <span class="font-semibold uppercase tracking-wide">{{ $latestTenantUpdate->status }}</span>
                    </div>
                @endif
                @if(!empty($config['github_repo_url']))
                    <div class="mt-4 border-t border-slate-200 pt-3">
                        <p class="text-xs text-slate-600">Release notes and full changelog:</p>
                        <a href="{{ rtrim($config['github_repo_url'], '/') }}/releases" target="_blank" rel="noopener noreferrer" class="mt-1 inline-flex text-sm font-semibold text-violet-700 hover:text-violet-600">
                            View release history
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <div class="rounded-xl border rr-border rr-surface p-6 shadow-rr lg:col-span-3">
            <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-700">Need help?</h2>
                <p class="mt-1 text-sm text-slate-600">
                    Send your question or issue below. Your message goes directly to Super Admin support.
                </p>
            </div>
            <h2 class="mt-5 text-sm font-semibold uppercase tracking-wide text-slate-700">Conversation with Super Admin</h2>
            <div id="tenant-chat-thread" class="mt-3 max-h-[460px] space-y-3 overflow-y-auto rounded-lg border border-slate-200 bg-slate-50 p-3 sm:p-4">
                @forelse($messages as $chat)
                    @php
                        $isTenant = $chat->sender_role === \App\Models\TenantChatMessage::SENDER_TENANT;
                        $senderLabel = $isTenant ? 'You' : 'Super Admin';
                    @endphp
                    <div class="flex {{ $isTenant ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-[88%] rounded-2xl {{ $isTenant ? 'rounded-br-md border border-violet-300 bg-violet-100 text-violet-900' : 'rounded-bl-md border border-slate-300 bg-white text-slate-900' }} px-3 py-2 text-sm shadow-sm">
                            <p class="text-[11px] font-semibold {{ $isTenant ? 'text-violet-700' : 'text-slate-600' }}">{{ $senderLabel }}</p>
                            <p class="mt-1 whitespace-pre-wrap">{{ $chat->message }}</p>
                            <p class="mt-1 text-[11px] {{ $isTenant ? 'text-violet-700/80' : 'text-slate-500' }}">{{ $chat->created_at?->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">No messages yet. Start the conversation below.</p>
                @endforelse
            </div>

            <form method="POST" action="{{ route('admin.about.messages.store') }}" class="mt-4 border-t border-slate-200 pt-4">
                @csrf
                <label for="message" class="rr-label">Reply</label>
                <textarea
                    id="message"
                    name="message"
                    rows="3"
                    maxlength="3000"
                    required
                    class="rr-input mt-1"
                    placeholder="Write your message...">{{ old('message') }}</textarea>
                @error('message')
                    <p class="mt-2 text-xs text-rose-300">{{ $message }}</p>
                @enderror
                <div class="mt-3 flex justify-end">
                    <button type="submit" class="inline-flex items-center rounded-lg rr-btn-primary px-4 py-2 text-sm font-semibold">
                        Send message
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const thread = document.getElementById('tenant-chat-thread');
        if (thread) {
            thread.scrollTop = thread.scrollHeight;
        }
    });
</script>
@endpush
