<?php

namespace App\Support;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use Stancl\Tenancy\Database\Models\Tenant as TenancyTenant;

class TenantDatabaseName
{
    public static function generate(int $tenantId): string
    {
        $key = (string) config('app.key', 'rentride');
        $hash = hash_hmac('sha256', 'tenant-db:' . $tenantId, $key);

        return 'tenant_' . substr($hash, 0, 20);
    }

    public static function legacy(int $tenantId): string
    {
        return 'tenant_' . $tenantId;
    }

    public static function fromTenancyData(?TenancyTenant $tenancyTenant): ?string
    {
        if (! $tenancyTenant) {
            return null;
        }

        $data = $tenancyTenant->data;
        if (! is_array($data)) {
            $decoded = json_decode((string) $data, true);
            $data = is_array($decoded) ? $decoded : [];
        }

        $database = trim((string) ($data['database'] ?? ''));
        if ($database !== '') {
            return $database;
        }

        $encrypted = trim((string) ($data['database_encrypted'] ?? ''));
        if ($encrypted === '') {
            return null;
        }

        try {
            $decrypted = trim(Crypt::decryptString($encrypted));

            return $decrypted !== '' ? $decrypted : null;
        } catch (DecryptException $e) {
            return null;
        }
    }

    public static function forTenantId(int $tenantId): string
    {
        $tenancyTenant = TenancyTenant::query()->find((string) $tenantId);
        $stored = self::fromTenancyData($tenancyTenant);

        return $stored ?? self::generate($tenantId);
    }

    public static function setOnTenancyTenant(TenancyTenant $tenancyTenant, string $databaseName): void
    {
        $data = $tenancyTenant->data;
        if (! is_array($data)) {
            $decoded = json_decode((string) $data, true);
            $data = is_array($decoded) ? $decoded : [];
        }

        $data['database'] = $databaseName;
        $data['database_encrypted'] = Crypt::encryptString($databaseName);
        $tenancyTenant->data = $data;
    }
}
