<?php

namespace App\Support;

use App\Models\Tenant;

class TenantRuntimeVersion
{
    public static function currentForTenant(int $tenantId, string $fallbackVersion): string
    {
        $tenant = Tenant::query()->find($tenantId);
        if (! $tenant) {
            return $fallbackVersion;
        }

        $version = trim((string) ($tenant->app_version ?? ''));

        return $version !== '' ? $version : $fallbackVersion;
    }

    public static function appliedAtForTenant(int $tenantId): ?string
    {
        $tenant = Tenant::query()->find($tenantId);
        if (! $tenant || ! $tenant->app_version_applied_at) {
            return null;
        }

        return $tenant->app_version_applied_at->toIso8601String();
    }

    public static function setApplied(int $tenantId, string $version): void
    {
        Tenant::query()
            ->whereKey($tenantId)
            ->update([
                'app_version' => trim($version),
                'app_version_applied_at' => now(),
            ]);
    }

    public static function isAtLeast(string $currentVersion, string $minimumVersion): bool
    {
        $current = ltrim(trim($currentVersion), 'vV');
        $minimum = ltrim(trim($minimumVersion), 'vV');
        if ($current === '' || $minimum === '') {
            return false;
        }

        $looksComparable = static fn (string $v): bool => (bool) preg_match('/^\d+(\.\d+){0,3}([\-+].*)?$/', $v);
        if (! $looksComparable($current) || ! $looksComparable($minimum)) {
            return false;
        }

        return version_compare($current, $minimum, '>=');
    }

}
