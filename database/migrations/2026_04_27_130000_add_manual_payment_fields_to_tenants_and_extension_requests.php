<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('signup_payment_method', 50)->nullable()->after('subscription_plan');
            $table->string('signup_payment_reference', 120)->nullable()->after('signup_payment_method');
            $table->string('signup_payment_proof_path')->nullable()->after('signup_payment_reference');
            $table->text('signup_payment_notes')->nullable()->after('signup_payment_proof_path');
        });

        Schema::table('plan_extension_requests', function (Blueprint $table) {
            $table->string('payment_method', 50)->nullable()->after('requested_plan');
            $table->string('payment_reference', 120)->nullable()->after('payment_method');
            $table->string('payment_proof_path')->nullable()->after('payment_reference');
            $table->text('payment_notes')->nullable()->after('payment_proof_path');
        });
    }

    public function down(): void
    {
        Schema::table('plan_extension_requests', function (Blueprint $table) {
            $table->dropColumn([
                'payment_method',
                'payment_reference',
                'payment_proof_path',
                'payment_notes',
            ]);
        });

        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn([
                'signup_payment_method',
                'signup_payment_reference',
                'signup_payment_proof_path',
                'signup_payment_notes',
            ]);
        });
    }
};

