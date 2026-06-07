<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Sector;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sector_id' => Sector::factory(),
            'name' => fake()->words(2, true),
        ];
    }
}
