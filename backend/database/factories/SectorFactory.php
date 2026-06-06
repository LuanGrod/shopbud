<?php

namespace Database\Factories;

use App\Models\Sector;
use App\Models\Template;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Sector>
 */
class SectorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'template_id' => Template::factory(),
            'name' => fake()->words(2, true),
            'order' => fake()->numberBetween(1, 10),
        ];
    }
}
