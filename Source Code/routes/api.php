<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductApiController;
use App\Http\Controllers\Api\OrderApiController;
use App\Http\Controllers\Api\FileUploadController;
use App\Http\Controllers\Api\PreOrderTokenController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// API v1 routes - Protected by API key
Route::prefix('v1')->middleware('api.key')->group(function () {

    // Products
    Route::get('/products', [ProductApiController::class, 'index']);
    Route::get('/products/{id}', [ProductApiController::class, 'show']);

    // Orders
    Route::post('/orders/create', [OrderApiController::class, 'create']);
    Route::post('/orders/track', [OrderApiController::class, 'track']);

    // File Uploads
    Route::post('/files/upload', [FileUploadController::class, 'upload']);
    Route::post('/files/link-to-order', [FileUploadController::class, 'linkToOrder']);
    Route::get('/files/order/{orderId}', [FileUploadController::class, 'getOrderFiles']);
    Route::delete('/files/{attachmentId}', [FileUploadController::class, 'delete']);

    // Pre-Order Tokens (Staff only - requires auth)
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/pre-order-tokens/generate', [PreOrderTokenController::class, 'generateToken']);
        Route::get('/pre-order-tokens/my-tokens', [PreOrderTokenController::class, 'getMyTokens']);
    });
});

// Public routes for customer order submission (no auth required)
Route::prefix('v1')->group(function () {
    // Get token details (customer form loads this)
    Route::get('/pre-order-tokens/{token}', [PreOrderTokenController::class, 'getToken']);

    // Submit customer order
    Route::post('/pre-order-tokens/submit', [PreOrderTokenController::class, 'submitOrder']);
});

// Health check endpoint (no auth required)
Route::get('/health', function () {
    return response()->json([
        'success' => true,
        'message' => 'CarbonCraft API is running',
        'timestamp' => now()->toIso8601String()
    ]);
});
