<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    // database/factories/ProductFactory.php
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->words(3, true),
            'selling_price' => $this->faker->numberBetween(5000, 150000)
        ];
    }
}
