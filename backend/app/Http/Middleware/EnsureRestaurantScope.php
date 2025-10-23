<?php

namespace App\Http\Middleware;

use App\Models\MenuCategory;
use App\Models\Order;
use App\Models\Restaurant;
use App\Models\Table;
use Closure;
use Illuminate\Http\Request;

class EnsureRestaurantScope
{
    public function handle(Request $request, Closure $next)
    {
        $restaurantId = $request->route('restaurant');
        if (!$restaurantId || !Restaurant::query()->whereKey($restaurantId)->exists()) {
            abort(404);
        }

        // Validate that bound models belong to the restaurant
        if ($tableId = $request->route('table')) {
            $table = Table::findOrFail($tableId);
            if ((int) $table->restaurant_id !== (int) $restaurantId) {
                abort(404);
            }
        }

        if ($orderId = $request->route('order')) {
            $order = Order::findOrFail($orderId);
            if ((int) $order->restaurant_id !== (int) $restaurantId) {
                abort(404);
            }
        }

        if ($categoryId = $request->route('category')) {
            $category = MenuCategory::findOrFail($categoryId);
            if ((int) $category->restaurant_id !== (int) $restaurantId) {
                abort(404);
            }
        }

        return $next($request);
    }
}
