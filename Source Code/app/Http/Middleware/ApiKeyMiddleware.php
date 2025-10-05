<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiKeyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('X-API-Key');

        // Get API key from environment
        $validApiKey = config('app.api_key');

        if (!$apiKey || $apiKey !== $validApiKey) {
            return response()->json([
                'success' => false,
                'error' => 'Unauthorized - Invalid API Key'
            ], 401);
        }

        return $next($request);
    }
}
