<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'mongodb_id',
        'product_id',
        'name',
        'slug',
        'display_name',
        'price',
        'discount_percentage',
        'final_price',
        'main_image',
        'variant_images',
        'category',
        'is_active',
        'stock',
        'description',
    ];

    protected $casts = [
        'variant_images' => 'array',
        'is_active' => 'boolean',
        'price' => 'integer',
        'discount_percentage' => 'integer',
        'final_price' => 'integer',
        'stock' => 'integer',
    ];

    /**
     * Get formatted price for display
     */
    public function getFormattedPriceAttribute()
    {
        return '₹' . number_format($this->price, 0);
    }

    /**
     * Get formatted final price for display
     */
    public function getFormattedFinalPriceAttribute()
    {
        return '₹' . number_format($this->final_price, 0);
    }

    /**
     * Scope to get only active products
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter by category
     */
    public function scopeCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Get savings amount
     */
    public function getSavingsAttribute()
    {
        return $this->price - $this->final_price;
    }
}
