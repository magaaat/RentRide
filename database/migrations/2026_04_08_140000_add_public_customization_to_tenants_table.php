<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            if (! Schema::hasColumn('tenants', 'public_tagline')) {
                $table->string('public_tagline')->nullable();
            }
            if (! Schema::hasColumn('tenants', 'website_url')) {
                $table->string('website_url', 512)->nullable();
            }
            if (! Schema::hasColumn('tenants', 'public_booking_notes')) {
                $table->text('public_booking_notes')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            if (Schema::hasColumn('tenants', 'public_booking_notes')) {
                $table->dropColumn('public_booking_notes');
            }
            if (Schema::hasColumn('tenants', 'website_url')) {
                $table->dropColumn('website_url');
            }
            if (Schema::hasColumn('tenants', 'public_tagline')) {
                $table->dropColumn('public_tagline');
            }
        });
    }
};
