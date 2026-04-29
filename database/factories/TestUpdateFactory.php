<?php

namespace Database\Factories;

use App\Models\TestUpdate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TestUpdate>
 */
class TestUpdateFactory extends Factory
{
    private static int $planSequence = 0;

    public function definition(): array
    {
        self::$planSequence++;

        return [
            'name' => fake()->sentence(4),
            'plan_id' => ((self::$planSequence - 1) % 50) + 1,
        ];
    }
}
