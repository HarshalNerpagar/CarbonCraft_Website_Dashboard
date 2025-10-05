<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductApiController;
use App\Http\Controllers\Api\OrderApiController;

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
});

// Health check endpoint (no auth required)
Route::get('/health', function () {
    return response()->json([
        'success' => true,
        'message' => 'CarbonCraft API is running',
        'timestamp' => now()->toIso8601String()
    ]);
});
