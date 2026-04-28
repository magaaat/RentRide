<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        try {
            DB::statement("ALTER TABLE users MODIFY role ENUM('super_admin','admin','customer','branch_manager','reservation_staff','fleet_maintenance_staff','cashier_billing_staff','custom_staff') NOT NULL DEFAULT 'customer'");
        } catch (\Throwable $e) {
            // Best effort for non-MySQL drivers.
        }
    }

    public function down(): void
    {
        try {
            DB::statement("ALTER TABLE users MODIFY role ENUM('super_admin','admin','customer','branch_manager','reservation_staff','fleet_maintenance_staff','cashier_billing_staff') NOT NULL DEFAULT 'customer'");
        } catch (\Throwable $e) {
            // Best effort for non-MySQL drivers.
        }
    }
};
