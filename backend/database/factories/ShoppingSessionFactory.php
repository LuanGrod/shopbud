<?php

namespace Database\Factories;

use App\Models\Template;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\ShoppingSession>
 */
class ShoppingSessionFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'template_id' => Template::factory(),
            'status' => 'active',
            'snapshot' => ['name' => fake()->words(2, true), 'sectors' => []],
            'expires_at' => now()->addDay(),
        ];
    }
}
