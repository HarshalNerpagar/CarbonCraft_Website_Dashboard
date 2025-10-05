<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductApiController extends Controller
{
    /**
     * Get all active products
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $query = Product::where('is_active', true);

            // Filter by category if provided
            if ($request->has('category')) {
                $query->where('category', $request->category);
            }

            // Search by name
            if ($request->has('search')) {
                $query->where('display_name', 'like', "%{$request->search}%");
            }

            $products = $query->orderBy('display_name', 'asc')->get();

            // Transform products to match website format
            $formattedProducts = $products->map(function($product) {
                return [
                    '_id' => $product->mongodb_id,
                    'id' => $product->product_id,
                    'name' => $product->name,
                    'display_name' => $product->display_name,
                    'price' => $product->price,
                    'discount_p' => $product->discount_percentage,
                    'final_price' => $product->final_price,
                    'images' => [
                        'main' => $product->main_image,
                        'variants' => json_decode($product->variant_images, true) ?? []
                    ],
                    'category' => $product->category,
                    'stock' => $product->stock,
                    'is_active' => $product->is_active
                ];
            });

            return response()->json([
                'success' => true,
                'count' => $formattedProducts->count(),
                'products' => $formattedProducts
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch products',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get single product by ID
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $product = Product::where('product_id', $id)
                             ->orWhere('mongodb_id', $id)
                             ->orWhere('slug', $id)
                             ->first();

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'error' => 'Product not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'product' => [
                    '_id' => $product->mongodb_id,
                    'id' => $product->product_id,
                    'name' => $product->name,
                    'display_name' => $product->display_name,
                    'slug' => $product->slug,
                    'price' => $product->price,
                    'discount_p' => $product->discount_percentage,
                    'final_price' => $product->final_price,
                    'images' => [
                        'main' => $product->main_image,
                        'variants' => json_decode($product->variant_images, true) ?? []
                    ],
                    'category' => $product->category,
                    'description' => $product->description,
                    'stock' => $product->stock,
                    'is_active' => $product->is_active
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch product',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
