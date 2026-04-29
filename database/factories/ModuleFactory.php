<?php

namespace Database\Factories;

use App\Models\Module;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Module>
 */
class ModuleFactory extends Factory
{
    private static int $planSequence = 0;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        self::$planSequence++;

        return [
            'name' => fake()->sentence(4),
            'plan_id' => ((self::$planSequence - 1) % 50) + 1,
        ];
    }
}
