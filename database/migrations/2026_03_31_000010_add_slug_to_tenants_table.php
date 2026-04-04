<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            if (! Schema::hasColumn('tenants', 'slug')) {
                $table->string('slug')->nullable()->unique()->after('company_name');
            }
        });

        // Best-effort backfill for existing rows.
        $existingSlugs = [];
        DB::table('tenants')
            ->select(['id', 'company_name', 'slug'])
            ->orderBy('id')
            ->chunk(200, function ($rows) use (&$existingSlugs) {
                foreach ($rows as $row) {
                    if (! empty($row->slug)) {
                        $existingSlugs[(string) $row->slug] = true;
                        continue;
                    }

                    $base = Str::slug((string) ($row->company_name ?? ''));
                    if ($base === '') {
                        $base = 'tenant-' . $row->id;
                    }

                    $slug = $base;
                    if (isset($existingSlugs[$slug])) {
                        $slug = $base . '-' . $row->id;
                    }

                    DB::table('tenants')->where('id', $row->id)->update(['slug' => $slug]);
                    $existingSlugs[$slug] = true;
                }
            });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            if (Schema::hasColumn('tenants', 'slug')) {
                $table->dropUnique(['slug']);
                $table->dropColumn('slug');
            }
        });
    }
};

