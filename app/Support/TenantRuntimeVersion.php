<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

class TenantRuntimeVersion
{
    public static function currentForTenant(int $tenantId, string $fallbackVersion): string
    {
        $record = self::read($tenantId);
        $version = trim((string) ($record['version'] ?? ''));

        return $version !== '' ? $version : $fallbackVersion;
    }

    public static function appliedAtForTenant(int $tenantId): ?string
    {
        $record = self::read($tenantId);
        $appliedAt = trim((string) ($record['applied_at'] ?? ''));

        return $appliedAt !== '' ? $appliedAt : null;
    }

    public static function setApplied(int $tenantId, string $version, string $packagePath): void
    {
        $payload = [
            'version' => trim($version),
            'package_path' => trim($packagePath),
            'applied_at' => now()->toIso8601String(),
        ];

        Storage::disk('local')->put(self::path($tenantId), json_encode($payload, JSON_PRETTY_PRINT));
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

    protected static function path(int $tenantId): string
    {
        return "tenant-runtime/tenant-{$tenantId}.json";
    }

    protected static function read(int $tenantId): array
    {
        $path = self::path($tenantId);
        if (! Storage::disk('local')->exists($path)) {
            return [];
        }

        $raw = Storage::disk('local')->get($path);
        $decoded = json_decode($raw, true);

        return is_array($decoded) ? $decoded : [];
    }
}
