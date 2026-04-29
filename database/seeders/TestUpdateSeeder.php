<?php

namespace Database\Seeders;

use App\Models\TestUpdate;
use Illuminate\Database\Seeder;

class TestUpdateSeeder extends Seeder
{
    public function run(): void
    {
        TestUpdate::factory()->count(50)->create();
    }
}
