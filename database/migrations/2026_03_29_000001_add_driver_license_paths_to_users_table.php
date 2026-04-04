<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('driver_license_front_path')->nullable()->after('address');
            $table->string('driver_license_back_path')->nullable()->after('driver_license_front_path');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['driver_license_front_path', 'driver_license_back_path']);
        });
    }
};
