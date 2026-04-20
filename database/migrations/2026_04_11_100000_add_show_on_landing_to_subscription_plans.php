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
            if (! Schema::hasColumn('subscription_plans', 'show_on_landing')) {
                $table->boolean('show_on_landing')->default(true)->after('is_active');
            }
        });

        DB::table('subscription_plans')->update(['show_on_landing' => true]);
    }

    public function down(): void
    {
        Schema::table('subscription_plans', function (Blueprint $table) {
            if (Schema::hasColumn('subscription_plans', 'show_on_landing')) {
                $table->dropColumn('show_on_landing');
            }
        });
    }
};
