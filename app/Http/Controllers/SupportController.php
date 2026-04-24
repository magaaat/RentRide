<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\TenantChatMessage;
use App\Models\TenantInquiry;
use App\Models\TenantUpdateRequest;
use App\Support\TenantReleaseUpdater;
use App\Support\TenantRuntimeVersion;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;
use Throwable;

class SupportController extends Controller
{
    public function tenantAbout(Request $request): View
    {
        $user = Auth::user();
        abort_unless($user?->isTenantUser() && $user->tenant_id, 403);
        $tenant = Tenant::query()->find((int) $user->tenant_id);
        $currentVersion = TenantRuntimeVersion::currentForTenant(
            tenantId: (int) $user->tenant_id,
            fallbackVersion: (string) config('rentride.version', '')
        );

        $messages = TenantChatMessage::query()
            ->with('user:id,name')
            ->where('tenant_id', (int) $user->tenant_id)
            ->orderBy('id')
            ->get();
        $releaseInfo = $this->githubLatestRelease(true);
        $latestTenantUpdate = TenantUpdateRequest::query()
            ->where('tenant_id', (int) $user->tenant_id)
            ->where('status', TenantUpdateRequest::STATUS_APPLIED)
            ->latest('id')
            ->first();

        return view('support.tenant-about', [
            'config' => config('rentride'),
            'messages' => $messages,
            'releaseInfo' => $releaseInfo,
            'currentVersion' => $currentVersion,
            'runtimeAppliedAt' => TenantRuntimeVersion::appliedAtForTenant((int) $user->tenant_id),
            'latestTenantUpdate' => $latestTenantUpdate,
            'versionStatus' => $this->versionStatus(
                $currentVersion,
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
            ->route('admin.about');
    }

    public function downloadTenantUpdate(Request $request, TenantReleaseUpdater $updater): RedirectResponse
    {
        $user = Auth::user();
        abort_unless($user?->isTenantUser() && $user->tenant_id, 403);

        $releaseInfo = $this->githubLatestRelease();
        $targetVersion = trim((string) ($releaseInfo['tag_name'] ?? ''));
        if (! ($releaseInfo['available'] ?? false) || $targetVersion === '') {
            return redirect()
                ->route('admin.about')
                ->with('error', 'Unable to submit update request right now because latest release is unavailable.');
        }

        $tenant = Tenant::query()->find((int) $user->tenant_id);
        if (! $tenant) {
            return redirect()
                ->route('admin.about')
                ->with('error', 'Unable to apply update because tenant context was not found.');
        }

        $releaseLabel = $targetVersion;
        if (($releaseInfo['is_draft'] ?? false) === true) {
            $releaseLabel .= ' (draft)';
        } elseif (($releaseInfo['is_prerelease'] ?? false) === true) {
            $releaseLabel .= ' (pre-release)';
        }

        // Let the HTTP client release resources before spawning git (avoids rare Windows DNS/thread issues).
        gc_collect_cycles();

        try {
            $result = $updater->applyTag($targetVersion);
        } catch (Throwable $e) {
            report($e);

            TenantUpdateRequest::query()->create([
                'tenant_id' => (int) $tenant->id,
                'requested_by' => (int) $user->id,
                'target_version' => $targetVersion,
                'source_release_url' => trim((string) ($releaseInfo['html_url'] ?? '')),
                'is_draft' => (bool) ($releaseInfo['is_draft'] ?? false),
                'is_prerelease' => (bool) ($releaseInfo['is_prerelease'] ?? false),
                'status' => TenantUpdateRequest::STATUS_FAILED,
            ]);

            TenantChatMessage::query()->create([
                'tenant_id' => (int) $tenant->id,
                'user_id' => (int) $user->id,
                'sender_role' => TenantChatMessage::SENDER_TENANT,
                'message' => "[Tenant Update Failed]\n"
                    . "Version: {$releaseLabel}\n"
                    . "Reason: " . trim($e->getMessage()),
            ]);

            return redirect()
                ->route('admin.about')
                ->with('error', 'Update failed on this device. ' . trim($e->getMessage()));
        }

        TenantRuntimeVersion::setApplied(
            tenantId: (int) $tenant->id,
            version: $targetVersion
        );

        $updateLog = TenantUpdateRequest::query()->create([
            'tenant_id' => (int) $tenant->id,
            'requested_by' => (int) $user->id,
            'target_version' => $targetVersion,
            'source_release_url' => trim((string) ($releaseInfo['html_url'] ?? '')),
            'is_draft' => (bool) ($releaseInfo['is_draft'] ?? false),
            'is_prerelease' => (bool) ($releaseInfo['is_prerelease'] ?? false),
            'status' => TenantUpdateRequest::STATUS_APPLIED,
            'applied_by' => (int) $user->id,
            'applied_at' => now(),
        ]);

        $message = "[Tenant Update Applied]\n"
            . "Tenant applied {$releaseLabel} by running update commands on this device.\n"
            . "Update Log ID: #{$updateLog->id}\n"
            . "Release URL: " . trim((string) ($releaseInfo['html_url'] ?? 'N/A')) . "\n"
            . "Updater steps: " . count($result['steps'] ?? []) . "\n"
            . "Applied by: {$user->name}";

        TenantChatMessage::query()->create([
            'tenant_id' => (int) $tenant->id,
            'user_id' => (int) $user->id,
            'sender_role' => TenantChatMessage::SENDER_TENANT,
            'message' => $message,
        ]);

        return redirect()
            ->route('admin.about')
            ->with('status', "Update {$releaseLabel} applied for this tenant and synced across devices.");
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
        $releaseInfo = $this->githubLatestRelease(true);

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
            ->route('superadmin.about');
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
            ->route('superadmin.about', ['tenant_id' => $tenant->id]);
    }

    protected function githubLatestRelease(bool $forceRefresh = false): array
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
        $cacheBaseKey = 'rentride:github:latest-release:v2:' . $repo;
        $successCacheKey = $cacheBaseKey . ':success';
        $failureCacheKey = $cacheBaseKey . ':failure';

        if ($forceRefresh) {
            Cache::forget($successCacheKey);
            Cache::forget($failureCacheKey);
        }

        $cachedSuccess = Cache::get($successCacheKey);
        if (is_array($cachedSuccess) && ($cachedSuccess['available'] ?? false)) {
            return $cachedSuccess;
        }

        $cachedFailure = Cache::get($failureCacheKey);
        if (is_array($cachedFailure) && ! ($cachedFailure['available'] ?? false)) {
            return $cachedFailure;
        }

        $result = $this->fetchGithubLatestRelease($repo, $timeout);

        if ($result['available'] ?? false) {
            Cache::put($successCacheKey, $result, now()->addMinutes($cacheMinutes));
            Cache::forget($failureCacheKey);
        } else {
            Cache::put($failureCacheKey, $result, now()->addMinute());
        }

        return $result;
    }

    protected function fetchGithubLatestRelease(string $repo, int $timeout): array
    {
        $token = trim((string) config('rentride.github_token', ''));
        $verifySsl = (bool) config('rentride.github_release_verify_ssl', true);
        $usedInsecureRetry = false;

        $request = Http::acceptJson()
            ->timeout($timeout)
            ->withHeaders([
                'User-Agent' => 'RentRide-App',
            ]);

        if (! $verifySsl) {
            $request = $request->withoutVerifying();
        }

        if ($token !== '') {
            $request = $request->withToken($token);
        }

        try {
            $response = $request->get("https://api.github.com/repos/{$repo}/releases/latest");
        } catch (ConnectionException) {
            if ($verifySsl) {
                try {
                    $response = $request->withoutVerifying()
                        ->get("https://api.github.com/repos/{$repo}/releases/latest");
                    $usedInsecureRetry = true;
                } catch (ConnectionException) {
                    return ['available' => false, 'reason' => 'connection_failed'];
                }
            } else {
                return ['available' => false, 'reason' => 'connection_failed'];
            }
        }

        if (! $response->successful()) {
            if ($response->status() === 404) {
                $fallbackRelease = $this->fetchMostRecentGithubRelease($request, $repo);
                if ($fallbackRelease !== null) {
                    return $fallbackRelease;
                }

                return ['available' => false, 'reason' => 'release_not_found'];
            }

            if (in_array($response->status(), [401, 403], true)) {
                return ['available' => false, 'reason' => 'auth_failed'];
            }

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
            'asset_download_url' => $this->resolveReleaseAssetDownloadUrl($data),
            'download_url' => $this->resolveReleaseDownloadUrl($data),
            'published_at' => trim((string) ($data['published_at'] ?? '')),
            'is_draft' => (bool) ($data['draft'] ?? false),
            'is_prerelease' => (bool) ($data['prerelease'] ?? false),
            'used_insecure_retry' => $usedInsecureRetry,
        ];
    }

    protected function fetchMostRecentGithubRelease(PendingRequest $request, string $repo): ?array
    {
        try {
            $response = $request->get("https://api.github.com/repos/{$repo}/releases", [
                'per_page' => 1,
            ]);
        } catch (ConnectionException) {
            return null;
        }

        if (! $response->successful()) {
            return null;
        }

        $release = $this->firstReleaseFromResponse($response);
        if ($release === null) {
            return null;
        }

        $tag = trim((string) ($release['tag_name'] ?? ''));
        if ($tag === '') {
            return null;
        }

        return [
            'available' => true,
            'tag_name' => $tag,
            'name' => trim((string) ($release['name'] ?? '')),
            'html_url' => trim((string) ($release['html_url'] ?? '')),
            'asset_download_url' => $this->resolveReleaseAssetDownloadUrl($release),
            'download_url' => $this->resolveReleaseDownloadUrl($release),
            'published_at' => trim((string) ($release['published_at'] ?? '')),
            'is_draft' => (bool) ($release['draft'] ?? false),
            'is_prerelease' => (bool) ($release['prerelease'] ?? false),
        ];
    }

    protected function firstReleaseFromResponse(Response $response): ?array
    {
        $payload = $response->json();
        if (! is_array($payload) || ! isset($payload[0]) || ! is_array($payload[0])) {
            return null;
        }

        return $payload[0];
    }

    protected function resolveReleaseDownloadUrl(array $release): string
    {
        $assetUrl = $this->resolveReleaseAssetDownloadUrl($release);
        if ($assetUrl !== '') {
            return $assetUrl;
        }

        // For tenant-side server downloads, use release archives when no asset exists.
        $zipballUrl = trim((string) ($release['zipball_url'] ?? ''));
        if ($zipballUrl !== '') {
            return $zipballUrl;
        }

        $tarballUrl = trim((string) ($release['tarball_url'] ?? ''));
        if ($tarballUrl !== '') {
            return $tarballUrl;
        }

        return '';
    }

    protected function resolveReleaseAssetDownloadUrl(array $release): string
    {
        $assets = $release['assets'] ?? [];
        if (is_array($assets)) {
            foreach ($assets as $asset) {
                $downloadUrl = trim((string) ($asset['browser_download_url'] ?? ''));
                if ($downloadUrl !== '') {
                    return $downloadUrl;
                }
            }
        }

        return '';
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
