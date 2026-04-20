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
            if (! Schema::hasColumn('subscription_plans', 'feature_tier')) {
                $table->string('feature_tier', 16)->nullable()->after('tier');
            }
        });

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            try {
                DB::statement('ALTER TABLE `subscription_plans` MODIFY `tier` VARCHAR(64) NULL');
            } catch (\Throwable $e) {
                //
            }
        }

        $rows = DB::table('subscription_plans')->select('id', 'tier')->get();
        foreach ($rows as $row) {
            $t = $row->tier;
            $feature = in_array($t, ['basic', 'standard', 'premium'], true) ? $t : 'basic';
            DB::table('subscription_plans')->where('id', $row->id)->update(['feature_tier' => $feature]);
        }
    }

    public function down(): void
    {
        Schema::table('subscription_plans', function (Blueprint $table) {
            if (Schema::hasColumn('subscription_plans', 'feature_tier')) {
                $table->dropColumn('feature_tier');
            }
        });
    }
};
