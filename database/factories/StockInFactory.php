<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class StockInFactory extends Factory
{
    public function definition(): array
    {
        $quantity = fake()->numberBetween(1, 50);
        $price = fake()->randomFloat(2, 10000, 1000000);

        return [
            'item_id' => Item::factory(),
            'quantity' => $quantity,
            'unit_price' => $price,
            'total_price' => $price * $quantity,
            'notes' => fake()->optional()->sentence(),
            'user_id' => User::factory(),
        ];
    }
}
