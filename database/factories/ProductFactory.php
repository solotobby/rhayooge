<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = fake()->words(3, true);

        return [
            'name' => ucwords($name),
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(100, 9999),
            'category' => fake()->randomElement(['Dresses', 'Tops', 'Bottoms', 'Outerwear', 'Sets', 'Accessories']),
            'price' => fake()->numberBetween(15000, 80000),
            'original_price' => null,
            'quantity' => 10,
            'commission_type' => 'percent',
            'commission_rate' => 10,
            'size_mode' => 'letter',
            'sizes' => ['S', 'M', 'L'],
            'featured' => false,
            'newest' => true,
            'description' => fake()->paragraph(2),
            'image' => 'https://images.unsplash.com/photo-1496747611176-843222e1e57c?auto=format&fit=crop&w=900&q=80',
        ];
    }
}
