<?php

namespace Database\Factories;

use App\Models\Bid;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Bid>
 */
class BidFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'bidder_name' => $this->faker->firstName(),
            'amount' => $this->faker->numberBetween(1000, 5000),
        ];
    }
}
