<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\TenantChatMessage;
use App\Models\TenantInquiry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class SupportController extends Controller
{
    public function tenantAbout(): View
    {
        $user = Auth::user();
        abort_unless($user?->isTenantUser() && $user->tenant_id, 403);

        $messages = TenantChatMessage::query()
            ->with('user:id,name')
            ->where('tenant_id', (int) $user->tenant_id)
            ->orderBy('id')
            ->get();
        $releaseInfo = $this->githubLatestRelease();

        return view('support.tenant-about', [
            'config' => config('rentride'),
            'messages' => $messages,
            'releaseInfo' => $releaseInfo,
            'versionStatus' => $this->versionStatus(
                (string) config('rentride.version', ''),
                $releaseInfo['tag_name'] ?? null
            ),
        ]);
    }

    public function storeTenantInquiry(Request $request): RedirectResponse
    {
        $user = Auth::user();
        abort_unless($user?->isTenantUser() && $user->tenant_id, 403);

        $data = $request->validate([
            'message' => ['required', 'string', 'max:3000'],
        ]);

        TenantInquiry::query()->create([
            'tenant_id' => (int) $user->tenant_id,
            'user_id' => (int) $user->id,
            'message' => trim($data['message']),
        ]);
        TenantChatMessage::query()->create([
            'tenant_id' => (int) $user->tenant_id,
            'user_id' => (int) $user->id,
            'sender_role' => TenantChatMessage::SENDER_TENANT,
            'message' => trim($data['message']),
        ]);

        return redirect()
            ->route('admin.about')
            ->with('success', 'Your message has been sent to Super Admin.');
    }

    public function superAdminAbout(Request $request): View
    {
        abort_unless(Auth::user()?->isSuperAdmin(), 403);

        $tenants = Tenant::query()
            ->orderBy('company_name')
            ->get(['id', 'company_name', 'domain', 'status']);
        $selectedTenantId = (int) ($request->integer('tenant_id') ?: ($tenants->first()->id ?? 0));

        $messages = collect();
        if ($selectedTenantId > 0) {
            $messages = TenantChatMessage::query()
                ->with('user:id,name')
                ->where('tenant_id', $selectedTenantId)
                ->orderBy('id')
                ->get();
        }
        $releaseInfo = $this->githubLatestRelease();

        return view('support.superadmin-about', [
            'config' => config('rentride'),
            'tenants' => $tenants,
            'selectedTenantId' => $selectedTenantId,
            'messages' => $messages,
            'releaseInfo' => $releaseInfo,
            'versionStatus' => $this->versionStatus(
                (string) config('rentride.version', ''),
                $releaseInfo['tag_name'] ?? null
            ),
        ]);
    }

    public function storeSuperAdminReply(Request $request, TenantInquiry $inquiry): RedirectResponse
    {
        abort_unless(Auth::user()?->isSuperAdmin(), 403);

        $data = $request->validate([
            'reply_message' => ['required', 'string', 'max:3000'],
        ]);

        $inquiry->update([
            'reply_message' => trim($data['reply_message']),
            'replied_by' => (int) Auth::id(),
            'replied_at' => now(),
        ]);

        return redirect()
            ->route('superadmin.about')
            ->with('success', 'Reply sent to tenant inquiry.');
    }

    public function storeSuperAdminChatMessage(Request $request, Tenant $tenant): RedirectResponse
    {
        abort_unless(Auth::user()?->isSuperAdmin(), 403);

        $data = $request->validate([
            'message' => ['required', 'string', 'max:3000'],
        ]);

        TenantChatMessage::query()->create([
            'tenant_id' => (int) $tenant->id,
            'user_id' => (int) Auth::id(),
            'sender_role' => TenantChatMessage::SENDER_SUPER_ADMIN,
            'message' => trim($data['message']),
        ]);

        return redirect()
            ->route('superadmin.about', ['tenant_id' => $tenant->id])
            ->with('success', 'Reply sent.');
    }

    protected function githubLatestRelease(): array
    {
        $repoUrl = trim((string) config('rentride.github_repo_url', ''));
        if ($repoUrl === '') {
            return ['available' => false, 'reason' => 'missing_repo_url'];
        }

        $repo = $this->extractGithubRepo($repoUrl);
        if (! $repo) {
            return ['available' => false, 'reason' => 'invalid_repo_url'];
        }

        $cacheMinutes = max(1, (int) config('rentride.github_release_cache_minutes', 30));
        $timeout = max(2, (int) config('rentride.github_release_timeout_seconds', 5));
        $cacheKey = 'rentride:github:latest-release:' . $repo;

        return Cache::remember($cacheKey, now()->addMinutes($cacheMinutes), function () use ($repo, $timeout) {
            $token = trim((string) config('rentride.github_token', ''));

            $request = Http::acceptJson()
                ->timeout($timeout)
                ->withHeaders([
                    'User-Agent' => 'RentRide-App',
                ]);

            if ($token !== '') {
                $request = $request->withToken($token);
            }

            $response = $request->get("https://api.github.com/repos/{$repo}/releases/latest");
            if (! $response->successful()) {
                return ['available' => false, 'reason' => 'request_failed'];
            }

            $data = $response->json();
            $tag = trim((string) ($data['tag_name'] ?? ''));
            if ($tag === '') {
                return ['available' => false, 'reason' => 'missing_tag'];
            }

            return [
                'available' => true,
                'tag_name' => $tag,
                'name' => trim((string) ($data['name'] ?? '')),
                'html_url' => trim((string) ($data['html_url'] ?? '')),
                'published_at' => trim((string) ($data['published_at'] ?? '')),
            ];
        });
    }

    protected function extractGithubRepo(string $url): ?string
    {
        $path = (string) parse_url($url, PHP_URL_PATH);
        $path = trim($path, '/');
        $parts = array_values(array_filter(explode('/', $path)));
        if (count($parts) < 2) {
            return null;
        }

        return $parts[0] . '/' . $parts[1];
    }

    protected function versionStatus(string $currentVersion, ?string $latestVersion): ?string
    {
        $current = ltrim(trim($currentVersion), 'vV');
        $latest = ltrim(trim((string) $latestVersion), 'vV');
        if ($current === '' || $latest === '') {
            return null;
        }

        if ($current === $latest) {
            return 'up_to_date';
        }

        $looksComparable = fn (string $v) => (bool) preg_match('/^\d+(\.\d+){0,3}([\-+].*)?$/', $v);
        if (! $looksComparable($current) || ! $looksComparable($latest)) {
            return null;
        }

        $cmp = version_compare($current, $latest);
        if ($cmp < 0) {
            return 'behind';
        }
        if ($cmp > 0) {
            return 'ahead';
        }

        return 'up_to_date';
    }
}
