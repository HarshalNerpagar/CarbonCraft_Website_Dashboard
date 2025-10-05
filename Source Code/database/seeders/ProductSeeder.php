<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Path to products.json in Website folder
        $jsonPath = base_path('../../Website/products.json');

        if (!file_exists($jsonPath)) {
            $this->command->error('❌ products.json not found at: ' . $jsonPath);
            $this->command->warn('Please make sure the Website folder is in the correct location.');
            return;
        }

        $this->command->info('📦 Reading products from products.json...');

        $products = json_decode(file_get_contents($jsonPath), true);

        if (!$products || !is_array($products)) {
            $this->command->error('❌ Invalid JSON in products.json');
            return;
        }

        $this->command->info('Found ' . count($products) . ' products. Importing...');

        $imported = 0;
        $updated = 0;

        foreach ($products as $product) {
            // Format display name (convert underscore/snake_case to Title Case)
            $displayName = str_replace('_', ' ', $product['name']);
            $displayName = ucwords(str_replace('&', '&', $displayName));

            // Generate slug
            $slug = Str::slug($product['name']);

            // Import or update product
            $existing = Product::where('mongodb_id', $product['_id'])->first();

            if ($existing) {
                $existing->update([
                    'product_id' => $product['id'],
                    'name' => $product['name'],
                    'slug' => $slug,
                    'display_name' => $displayName,
                    'price' => $product['price'],
                    'discount_percentage' => $product['discount_p'],
                    'final_price' => $product['final_price'],
                    'main_image' => $product['images']['main'],
                    'variant_images' => json_encode($product['images']['variants'] ?? []),
                    'category' => 'Metal Card',
                    'is_active' => true,
                    'stock' => 999,
                    'description' => "Premium {$displayName} Metal Card - Custom engraved design with multiple finish options. High-quality metal construction for a luxurious feel."
                ]);
                $updated++;
            } else {
                Product::create([
                    'mongodb_id' => $product['_id'],
                    'product_id' => $product['id'],
                    'name' => $product['name'],
                    'slug' => $slug,
                    'display_name' => $displayName,
                    'price' => $product['price'],
                    'discount_percentage' => $product['discount_p'],
                    'final_price' => $product['final_price'],
                    'main_image' => $product['images']['main'],
                    'variant_images' => json_encode($product['images']['variants'] ?? []),
                    'category' => 'Metal Card',
                    'is_active' => true,
                    'stock' => 999,
                    'description' => "Premium {$displayName} Metal Card - Custom engraved design with multiple finish options. High-quality metal construction for a luxurious feel."
                ]);
                $imported++;
            }
        }

        $this->command->info('');
        $this->command->info('✅ Product import completed!');
        $this->command->table(
            ['Status', 'Count'],
            [
                ['New Products', $imported],
                ['Updated Products', $updated],
                ['Total Products', Product::count()]
            ]
        );
    }
}
