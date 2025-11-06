<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = User::inRandomOrder()->first();
        return [
            "user_id" => $user->id,
            "order_number" => fake()->numberBetween(1,100),
            "total" => fake()->numberBetween(300,700),
            "status" => fake()->randomElement(['pending', 'processing', 'completed', 'cancelled'])
        ];
    }
}
