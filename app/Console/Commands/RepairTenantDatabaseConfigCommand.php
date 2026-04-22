<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Support\TenantDatabaseName;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Stancl\Tenancy\Database\Models\Domain as TenancyDomain;
use Stancl\Tenancy\Database\Models\Tenant as TenancyTenant;

class RepairTenantDatabaseConfigCommand extends Command
{
    protected $signature = 'tenant:repair-db-config';

    protected $description = 'Backfill tenancy data.database and domain rows for approved tenants';

    public function handle(): int
    {
        $tenants = Tenant::query()
            ->where('status', 'approved')
            ->whereNotNull('domain')
            ->get();

        foreach ($tenants as $tenant) {
            $tenancyTenant = TenancyTenant::firstOrCreate(
                ['id' => (string) $tenant->id],
                ['data' => []]
            );

            $storedDb = TenantDatabaseName::fromTenancyData($tenancyTenant) ?? TenantDatabaseName::generate((int) $tenant->id);
            TenantDatabaseName::setOnTenancyTenant($tenancyTenant, $storedDb);
            $tenancyTenant->save();

            $dbName = $storedDb;
            $centralDb = env('DB_DATABASE', 'rentride');
            $tablesToClone = ['users', 'vehicles', 'customers', 'bookings', 'payments'];

            try {
                DB::statement("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            } catch (\Throwable $e) {
                $this->warn("Could not create DB $dbName: {$e->getMessage()}");
            }

            foreach ($tablesToClone as $table) {
                try {
                    DB::statement("CREATE TABLE IF NOT EXISTS `$dbName`.`$table` LIKE `$centralDb`.`$table`");
                } catch (\Throwable $e) {
                    // Best effort only.
                }
            }

            $encryptionSchemaStatements = [
                "ALTER TABLE `$dbName`.`customers` MODIFY `name` TEXT NOT NULL",
                "ALTER TABLE `$dbName`.`customers` MODIFY `email` TEXT NULL",
                "ALTER TABLE `$dbName`.`customers` MODIFY `phone` TEXT NULL",
                "ALTER TABLE `$dbName`.`customers` MODIFY `address` TEXT NULL",
                "ALTER TABLE `$dbName`.`users` MODIFY `name` TEXT NOT NULL",
                "ALTER TABLE `$dbName`.`users` MODIFY `phone` TEXT NULL",
                "ALTER TABLE `$dbName`.`users` MODIFY `address` TEXT NULL",
                "ALTER TABLE `$dbName`.`users` MODIFY `driver_license_front_path` TEXT NULL",
                "ALTER TABLE `$dbName`.`users` MODIFY `driver_license_back_path` TEXT NULL",
            ];
            foreach ($encryptionSchemaStatements as $sql) {
                try {
                    DB::statement($sql);
                } catch (\Throwable $e) {
                    // Best effort only.
                }
            }

            // Keep tenant users table compatible with staff RBAC columns/roles.
            try {
                DB::statement("ALTER TABLE `$dbName`.`users` MODIFY role ENUM('super_admin','admin','customer','branch_manager','reservation_staff','fleet_maintenance_staff','cashier_billing_staff') NOT NULL DEFAULT 'customer'");
            } catch (\Throwable $e) {
                // Best effort only.
            }
            try {
                DB::statement("ALTER TABLE `$dbName`.`users` ADD COLUMN staff_role VARCHAR(255) NULL AFTER role");
            } catch (\Throwable $e) {
                // Best effort only.
            }
            try {
                DB::statement("ALTER TABLE `$dbName`.`users` ADD COLUMN permissions JSON NULL AFTER staff_role");
            } catch (\Throwable $e) {
                // Best effort only.
            }
            try {
                DB::statement("ALTER TABLE `$dbName`.`users` ADD COLUMN is_active TINYINT(1) NOT NULL DEFAULT 1 AFTER permissions");
            } catch (\Throwable $e) {
                // Best effort only.
            }

            // Keep tenant vehicles table compatible with maintenance tracking fields.
            try {
                DB::statement("ALTER TABLE `$dbName`.`vehicles` ADD COLUMN maintenance_issue TEXT NULL AFTER description");
            } catch (\Throwable $e) {
                // Best effort only.
            }
            try {
                DB::statement("ALTER TABLE `$dbName`.`vehicles` ADD COLUMN maintenance_severity ENUM('low','medium','high','critical') NULL AFTER maintenance_issue");
            } catch (\Throwable $e) {
                // Best effort only.
            }
            try {
                DB::statement("ALTER TABLE `$dbName`.`vehicles` ADD COLUMN maintenance_reported_at DATE NULL AFTER maintenance_severity");
            } catch (\Throwable $e) {
                // Best effort only.
            }
            try {
                DB::statement("ALTER TABLE `$dbName`.`vehicles` ADD COLUMN maintenance_target_fix_at DATE NULL AFTER maintenance_reported_at");
            } catch (\Throwable $e) {
                // Best effort only.
            }
            try {
                DB::statement("ALTER TABLE `$dbName`.`vehicles` ADD COLUMN maintenance_fixed_at DATE NULL AFTER maintenance_target_fix_at");
            } catch (\Throwable $e) {
                // Best effort only.
            }
            try {
                DB::statement("ALTER TABLE `$dbName`.`vehicles` ADD COLUMN maintenance_cost_estimate DECIMAL(10,2) NULL AFTER maintenance_fixed_at");
            } catch (\Throwable $e) {
                // Best effort only.
            }

            TenancyDomain::firstOrCreate([
                'tenant_id' => (string) $tenant->id,
                'domain' => (string) $tenant->domain,
            ]);

            $this->line("Fixed tenant {$tenant->id} ({$tenant->company_name})");
        }

        $this->info('Tenant DB config repair complete.');

        return self::SUCCESS;
    }
}

