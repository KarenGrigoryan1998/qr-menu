<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\MenuCategory;
use App\Models\Restaurant;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    /**
     * Get restaurant menu with categories and items
     */
    public function index(Request $request, $restaurantId)
    {
        $restaurant = Restaurant::findOrFail($restaurantId);

        $categories = MenuCategory::where('restaurant_id', $restaurantId)
            ->with(['menus' => function ($query) {
                $query->where('available', true)
                    ->orderBy('name_hy');
            }])
            ->get();

        return response()->json([
            'restaurant' => [
                'id' => $restaurant->id,
                'name' => $restaurant->name,
                'slug' => $restaurant->slug,
                'settings' => $restaurant->settings,
            ],
            'categories' => $categories->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name_hy' => $category->name_hy,
                    'name_en' => $category->name_en,
                    'name_ru' => $category->name_ru,
                    'image_url' => $category->image_url,
                    'items' => $category->menus->map(function ($menu) {
                        return [
                            'id' => $menu->id,
                            'name_hy' => $menu->name_hy,
                            'name_en' => $menu->name_en,
                            'name_ru' => $menu->name_ru,
                            'description_hy' => $menu->description_hy,
                            'description_en' => $menu->description_en,
                            'description_ru' => $menu->description_ru,
                            'price' => $menu->price,
                            'image_url' => $menu->image_url,
                            'available' => $menu->available,
                        ];
                    }),
                ];
            }),
        ]);
    }

    /**
     * Get all categories for a restaurant
     */
    public function categories($restaurantId)
    {
        $categories = MenuCategory::where('restaurant_id', $restaurantId)
            ->get();

        return response()->json($categories);
    }

    /**
     * Get menu items by category
     */
    public function categoryItems($restaurantId, $categoryId)
    {
        $items = Menu::where('restaurant_id', $restaurantId)
            ->where('category_id', $categoryId)
            ->where('available', true)
            ->get();

        return response()->json($items);
    }
}
