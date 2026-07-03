<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::firstOrCreate(
            ['name' => 'Vintage Rolleiflex Camera'],
            [
                'description' => 'A beautifully preserved 1960s twin-lens reflex camera.',
                'starting_price' => 1500,
                'min_increment' => 100,
            ]
        );
    }
}
