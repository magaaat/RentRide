<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->text('maintenance_issue')->nullable()->after('description');
            $table->enum('maintenance_severity', ['low', 'medium', 'high', 'critical'])->nullable()->after('maintenance_issue');
            $table->date('maintenance_reported_at')->nullable()->after('maintenance_severity');
            $table->date('maintenance_target_fix_at')->nullable()->after('maintenance_reported_at');
            $table->date('maintenance_fixed_at')->nullable()->after('maintenance_target_fix_at');
            $table->decimal('maintenance_cost_estimate', 10, 2)->nullable()->after('maintenance_fixed_at');
        });
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn([
                'maintenance_issue',
                'maintenance_severity',
                'maintenance_reported_at',
                'maintenance_target_fix_at',
                'maintenance_fixed_at',
                'maintenance_cost_estimate',
            ]);
        });
    }
};
