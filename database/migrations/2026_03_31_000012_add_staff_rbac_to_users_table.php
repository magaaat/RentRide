<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'staff_role')) {
                $table->string('staff_role')->nullable()->after('role');
            }
            if (! Schema::hasColumn('users', 'permissions')) {
                $table->json('permissions')->nullable()->after('staff_role');
            }
            if (! Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('permissions');
            }
        });

        // Extend role enum with staff roles.
        try {
            DB::statement("ALTER TABLE users MODIFY role ENUM('super_admin','admin','customer','branch_manager','reservation_staff','fleet_maintenance_staff','cashier_billing_staff') NOT NULL DEFAULT 'customer'");
        } catch (\Throwable $e) {
            // Best effort for non-MySQL drivers.
        }
    }

    public function down(): void
    {
        try {
            DB::statement("ALTER TABLE users MODIFY role ENUM('super_admin','admin','customer') NOT NULL DEFAULT 'customer'");
        } catch (\Throwable $e) {
            // Best effort for non-MySQL drivers.
        }

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'is_active')) {
                $table->dropColumn('is_active');
            }
            if (Schema::hasColumn('users', 'permissions')) {
                $table->dropColumn('permissions');
            }
            if (Schema::hasColumn('users', 'staff_role')) {
                $table->dropColumn('staff_role');
            }
        });
    }
};

