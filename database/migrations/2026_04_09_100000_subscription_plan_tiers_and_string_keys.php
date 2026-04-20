<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscription_plans', function (Blueprint $table) {
            if (! Schema::hasColumn('subscription_plans', 'tier')) {
                $table->string('tier', 32)->nullable()->after('key');
            }
            if (! Schema::hasColumn('subscription_plans', 'sort_order')) {
                $table->unsignedSmallInteger('sort_order')->default(0)->after('is_active');
            }
        });

        $map = [
            'basic' => ['tier' => 'basic', 'sort_order' => 10],
            'standard' => ['tier' => 'standard', 'sort_order' => 20],
            'premium' => ['tier' => 'premium', 'sort_order' => 30],
        ];
        foreach ($map as $key => $meta) {
            DB::table('subscription_plans')->where('key', $key)->update($meta);
        }

        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            try {
                DB::statement('ALTER TABLE `tenants` MODIFY `subscription_plan` VARCHAR(64) NOT NULL DEFAULT "basic"');
            } catch (\Throwable $e) {
                //
            }
            try {
                DB::statement('ALTER TABLE `subscriptions` MODIFY `plan_name` VARCHAR(64) NOT NULL');
            } catch (\Throwable $e) {
                //
            }
        }
    }

    public function down(): void
    {
        Schema::table('subscription_plans', function (Blueprint $table) {
            if (Schema::hasColumn('subscription_plans', 'sort_order')) {
                $table->dropColumn('sort_order');
            }
            if (Schema::hasColumn('subscription_plans', 'tier')) {
                $table->dropColumn('tier');
            }
        });
    }
};
