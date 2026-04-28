<?php

namespace Database\Seeders;

use App\Models\Student;
use Illuminate\Database\Seeder;

class TenantStudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Student::updateOrCreate(
            ['student_id' => 'STU-0001'],
            ['name' => 'Juan Dela Cruz', 'address' => 'Manila']
        );

        Student::updateOrCreate(
            ['student_id' => 'STU-0002'],
            ['name' => 'Maria Santos', 'address' => 'Quezon City']
        );
    }
}
