<?php

namespace Database\Factories;

use App\Models\Module;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Module>
 */
class ModuleFactory extends Factory
{
    /**
     * Sequential plan id counter.
     */
    private static int $planIdSequence = 0;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        self::$planIdSequence++;

        return [
            'name' => $this->faker->realText(60),
            'plan_id' => ((self::$planIdSequence - 1) % 50) + 1,
        ];
    }
}
