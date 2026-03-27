<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // basic, standard, premium
            $table->string('name');
            $table->decimal('base_price', 10, 2)->default(0);
            $table->string('billing_period')->default('month');
            $table->string('currency', 3)->default('PHP');
            $table->string('discount_type')->default('none'); // none|percent|fixed
            $table->decimal('discount_value', 10, 2)->default(0);
            $table->json('features')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};

