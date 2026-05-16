<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(3, true),
            'category_id' => Category::factory(),
            'price' => fake()->randomFloat(2, 10000, 5000000),
            'stock' => 0,
            'description' => fake()->optional()->sentence(),
        ];
    }
}
