<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MaintenanceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'item_id' => Item::factory()->state(['stock' => 50]),
            'quantity' => 3,
            'description' => fake()->optional()->sentence(),
            'date' => fake()->date(),
            'status' => 'Pending',
            'user_id' => User::factory(),
        ];
    }
}
