<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class StockOutFactory extends Factory
{
    public function definition(): array
    {
        $quantity = 5;
        $price = fake()->randomFloat(2, 10000, 1000000);

        return [
            'item_id' => Item::factory()->state(['stock' => 100]),
            'quantity' => $quantity,
            'status' => fake()->randomElement(['Consumed', 'Damaged']),
            'unit_price' => $price,
            'total_price' => $price * $quantity,
            'notes' => fake()->optional()->sentence(),
            'user_id' => User::factory(),
        ];
    }
}
