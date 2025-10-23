<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use Illuminate\Http\JsonResponse;

class RestaurantController extends Controller
{
    /**
     * Get restaurant information
     */
    public function show(string $restaurantId): JsonResponse
    {
        try {
            $restaurant = Restaurant::findOrFail($restaurantId);

            return response()->json([
                'success' => true,
                'data' => $restaurant->only(['id', 'name', 'slug', 'image_url', 'settings']),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Restaurant not found',
            ], 404);
        }
    }
}
