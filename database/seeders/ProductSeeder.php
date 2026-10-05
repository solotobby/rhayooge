<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['slug' => 'ife-linen-dress', 'name' => 'Ifẹ́ Linen Dress', 'category' => 'Dresses', 'price' => 45000, 'sizes' => ['XS', 'S', 'M', 'L', 'XL'], 'featured' => true, 'newest' => true, 'description' => 'A fluid midi in sun-washed linen, cut to skim rather than cling. Made for warm afternoons and unhurried evenings.', 'image' => 'https://images.unsplash.com/photo-1496747611176-843222e1e57c?auto=format&fit=crop&w=900&q=80'],
            ['slug' => 'adeola-silk-blouse', 'name' => 'Adéọlá Silk Blouse', 'category' => 'Tops', 'price' => 28000, 'sizes' => ['XS', 'S', 'M', 'L'], 'featured' => true, 'newest' => false, 'description' => 'Soft drape, quiet sheen. A blouse that feels considered with trousers or tucked into a full skirt.', 'image' => 'https://images.unsplash.com/photo-1551488831-00ddcb6c6bd3?auto=format&fit=crop&w=900&q=80'],
            ['slug' => 'sahara-trousers', 'name' => 'Sahara Wide-Leg Trousers', 'category' => 'Bottoms', 'price' => 32000, 'sizes' => ['S', 'M', 'L', 'XL'], 'featured' => true, 'newest' => false, 'description' => 'Tailored ease in a sand-toned weave. High-rise, full-length, and endlessly wearable.', 'image' => 'https://images.unsplash.com/photo-1509631179647-0177331693ae?auto=format&fit=crop&w=900&q=80'],
            ['slug' => 'heritage-trench', 'name' => 'Heritage Trench', 'category' => 'Outerwear', 'price' => 78000, 'sizes' => ['S', 'M', 'L'], 'featured' => true, 'newest' => true, 'description' => 'Our signature coat. Double-breasted, belted, and cut in a warm camel that echoes the house palette.', 'image' => 'https://images.unsplash.com/photo-1520975661595-6453be3f7070?auto=format&fit=crop&w=900&q=80'],
            ['slug' => 'ember-wrap-set', 'name' => 'Ember Wrap Set', 'category' => 'Sets', 'price' => 65000, 'sizes' => ['XS', 'S', 'M', 'L'], 'featured' => true, 'newest' => true, 'description' => 'A two-piece wrap in terracotta silk-blend. Separate the pieces or wear them as a complete look.', 'image' => 'https://images.unsplash.com/photo-1469334031218-e382a71b716b?auto=format&fit=crop&w=900&q=80'],
            ['slug' => 'onyx-blazer', 'name' => 'Onyx Tailored Blazer', 'category' => 'Outerwear', 'price' => 72000, 'sizes' => ['S', 'M', 'L', 'XL'], 'featured' => false, 'newest' => false, 'description' => 'Sharp shoulders, a softened waist. Evening-ready structure without stiffness.', 'image' => 'https://images.unsplash.com/photo-1591369822096-ffd140ec948f?auto=format&fit=crop&w=900&q=80'],
            ['slug' => 'dawn-slip-skirt', 'name' => 'Dawn Slip Skirt', 'category' => 'Bottoms', 'price' => 25000, 'sizes' => ['XS', 'S', 'M', 'L'], 'featured' => false, 'newest' => true, 'description' => 'Bias-cut satin that moves with you. Pair with a knit by day and gold by night.', 'image' => 'https://images.unsplash.com/photo-1583496661160-fb5886a0aaaa?auto=format&fit=crop&w=900&q=80'],
            ['slug' => 'ivory-knit', 'name' => 'Ivory Knit Top', 'category' => 'Tops', 'price' => 22000, 'sizes' => ['XS', 'S', 'M', 'L', 'XL'], 'featured' => false, 'newest' => false, 'description' => 'Fine-gauge knit in cream. A closet essential with a slightly elongated sleeve.', 'image' => 'https://images.unsplash.com/photo-1434389677669-e08b4cac3105?auto=format&fit=crop&w=900&q=80'],
            ['slug' => 'terracotta-midi', 'name' => 'Terracotta Midi Dress', 'category' => 'Dresses', 'price' => 48000, 'sizes' => ['S', 'M', 'L', 'XL'], 'featured' => true, 'newest' => false, 'description' => 'Warm earth pigment, a defined waist, and a skirt that opens as you walk.', 'image' => 'https://images.unsplash.com/photo-1572804013309-59a88b7e92f1?auto=format&fit=crop&w=900&q=80'],
            ['slug' => 'gold-hoops', 'name' => 'Sculpted Gold Hoops', 'category' => 'Accessories', 'price' => 12000, 'sizes' => ['One size'], 'featured' => false, 'newest' => true, 'description' => 'Lightweight, slightly irregular hoops — jewellery that feels like an heirloom, not a trend.', 'image' => 'https://images.unsplash.com/photo-1535632066927-ab7c9ab60908?auto=format&fit=crop&w=900&q=80'],
            ['slug' => 'structured-tote', 'name' => 'Soft Structured Tote', 'category' => 'Accessories', 'price' => 35000, 'sizes' => ['One size'], 'featured' => false, 'newest' => false, 'description' => 'Room enough for a day, refined enough for dinner. Sand leather with chocolate stitching.', 'image' => 'https://images.unsplash.com/photo-1584917865442-de89df76afd3?auto=format&fit=crop&w=900&q=80'],
            ['slug' => 'column-dress', 'name' => 'Evening Column Dress', 'category' => 'Dresses', 'price' => 89000, 'sizes' => ['XS', 'S', 'M', 'L'], 'featured' => true, 'newest' => false, 'description' => 'A clean vertical line in deep espresso. For nights that ask for presence, not noise.', 'image' => 'https://images.unsplash.com/photo-1595777457583-95e059d581b8?auto=format&fit=crop&w=900&q=80'],
            ['slug' => 'cropped-vest', 'name' => 'Cropped Tailored Vest', 'category' => 'Tops', 'price' => 30000, 'sizes' => ['XS', 'S', 'M', 'L'], 'featured' => false, 'newest' => true, 'description' => 'Worn over silk or nothing at all. A precise crop that lengthens the frame.', 'image' => 'https://images.unsplash.com/photo-1551489186-cf8726f514f8?auto=format&fit=crop&w=900&q=80'],
            ['slug' => 'palazzo-pants', 'name' => 'Fluid Palazzo Pants', 'category' => 'Bottoms', 'price' => 38000, 'sizes' => ['S', 'M', 'L', 'XL'], 'featured' => false, 'newest' => false, 'description' => 'Volume with intention. A palazzo that still looks composed at a desk or a dinner table.', 'image' => 'https://images.unsplash.com/photo-1515372039744-b8f02a3ae446?auto=format&fit=crop&w=900&q=80'],
            ['slug' => 'lounge-set', 'name' => 'Dharmie Lounge Set', 'category' => 'Sets', 'price' => 42000, 'sizes' => ['S', 'M', 'L'], 'featured' => false, 'newest' => true, 'description' => 'Knit co-ords in ivory. Soft enough for home, polished enough to step outside.', 'image' => 'https://images.unsplash.com/photo-1618244972963-dbee1a7edc95?auto=format&fit=crop&w=900&q=80'],
            ['slug' => 'cashmere-wrap', 'name' => 'Cashmere Wrap', 'category' => 'Outerwear', 'price' => 55000, 'sizes' => ['One size'], 'featured' => false, 'newest' => false, 'description' => 'A generous wrap in chocolate cashmere-blend. The piece you reach for when the air turns cool.', 'image' => 'https://images.unsplash.com/photo-1544022613-e87ca75a784a?auto=format&fit=crop&w=900&q=80'],
        ];

        foreach ($products as $product) {
            Product::query()->updateOrCreate(['slug' => $product['slug']], $product);
        }
    }
}
