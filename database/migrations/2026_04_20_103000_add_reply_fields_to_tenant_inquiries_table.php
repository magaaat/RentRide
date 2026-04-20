<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tenant_inquiries', function (Blueprint $table) {
            $table->text('reply_message')->nullable()->after('message');
            $table->foreignId('replied_by')->nullable()->after('reply_message')->constrained('users')->nullOnDelete();
            $table->timestamp('replied_at')->nullable()->after('replied_by');
        });
    }

    public function down(): void
    {
        Schema::table('tenant_inquiries', function (Blueprint $table) {
            $table->dropConstrainedForeignId('replied_by');
            $table->dropColumn(['reply_message', 'replied_at']);
        });
    }
};
