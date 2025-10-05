<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('mongodb_id')->unique()->comment('Original MongoDB _id from products.json');
            $table->integer('product_id')->comment('Original id from products.json');
            $table->string('name')->index()->comment('Product slug name (e.g., grumpy, batman)');
            $table->string('slug')->unique()->comment('URL-friendly slug');
            $table->string('display_name')->comment('Formatted display name');
            $table->integer('price')->comment('Original price in rupees');
            $table->integer('discount_percentage')->comment('Discount percentage');
            $table->integer('final_price')->comment('Final price after discount');
            $table->string('main_image')->comment('Main product image URL');
            $table->json('variant_images')->nullable()->comment('Array of variant image URLs');
            $table->string('category')->default('Metal Card')->comment('Product category');
            $table->boolean('is_active')->default(true)->index()->comment('Product active status');
            $table->integer('stock')->default(999)->comment('Available stock (virtual for digital products)');
            $table->text('description')->nullable()->comment('Product description');
            $table->timestamps();

            // Indexes for faster queries
            $table->index(['is_active', 'category']);
            $table->index('final_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
