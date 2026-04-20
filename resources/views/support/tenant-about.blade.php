@extends('layouts.app')

@section('title', 'About')

@section('content')
<div class="mx-auto max-w-4xl">
    <h1 class="text-2xl font-semibold tracking-tight text-slate-900 sm:text-3xl">About RentRide</h1>
    <p class="mt-2 text-sm text-slate-600">Platform details, current build information, and direct help request to Super Admin.</p>

    <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-5">
        <div class="space-y-6 self-start rounded-xl border rr-border rr-surface p-6 shadow-rr lg:col-span-2">
        <div>
            <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-600">Current version</h2>
            <p class="mt-1 text-lg font-semibold text-slate-900">{{ $config['version'] }}</p>
            @if(!empty($config['git_sha']))
                <p class="mt-1 text-xs text-slate-600">Build: {{ $config['git_sha'] }}</p>
            @endif
            @if(($releaseInfo['available'] ?? false) && !empty($releaseInfo['tag_name']))
                <p class="mt-2 text-xs text-slate-600">
                    Latest GitHub release:
                    <a href="{{ $releaseInfo['html_url'] ?: rtrim((string) $config['github_repo_url'], '/') . '/releases' }}" target="_blank" rel="noopener noreferrer" class="font-semibold text-violet-700 hover:text-violet-600">
                        {{ $releaseInfo['tag_name'] }}
                    </a>
                </p>
                @if($versionStatus === 'up_to_date')
                    <p class="mt-1 text-xs font-semibold text-emerald-700">Status: Up to date</p>
                @elseif($versionStatus === 'behind')
                    <p class="mt-1 text-xs font-semibold text-amber-700">Status: Update available</p>
                @elseif($versionStatus === 'ahead')
                    <p class="mt-1 text-xs font-semibold text-sky-700">Status: Ahead of latest release</p>
                @endif
            @else
                <p class="mt-2 text-xs text-slate-500">
                    Latest release check unavailable. Set <code class="rounded bg-slate-100 px-1 py-0.5 text-[11px] text-slate-700">GITHUB_REPO_URL</code>
                    @if(($releaseInfo['reason'] ?? '') === 'request_failed')
                        and verify API access/token.
                    @endif
                </p>
            @endif
        </div>

        <div>
            <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-600">Release notes</h2>
            <p class="mt-2 text-sm text-slate-600 leading-relaxed">
                Product updates and fixes are published through GitHub Releases. Use it to check what changed between versions.
            </p>
            @if(!empty($config['github_repo_url']))
                <a href="{{ rtrim($config['github_repo_url'], '/') }}/releases" target="_blank" rel="noopener noreferrer" class="mt-3 inline-flex text-sm font-semibold text-violet-400 hover:text-violet-300">
                    View release history
                </a>
            @endif
        </div>

        <div>
            <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-600">Need help?</h2>
            <p class="mt-2 text-sm text-slate-600">
                Send your question or issue below. Your message goes directly to Super Admin.
            </p>
        </div>
        </div>

        <div class="rounded-xl border rr-border rr-surface p-6 shadow-rr lg:col-span-3">
            <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-700">Conversation with Super Admin</h2>
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
