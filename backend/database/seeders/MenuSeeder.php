<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\MenuCategory;
use App\Models\Restaurant;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $demoRestaurant = Restaurant::where('slug', 'demo-restaurant')->first();
        // Resolve a category by any of its multilingual name fields for robustness
        $byName = function (string $name) use ($demoRestaurant) {
            return MenuCategory::where('restaurant_id', $demoRestaurant->id)
                ->where(function ($q) use ($name) {
                    $q->where('name', $name)
                      ->orWhere('name_en', $name)
                      ->orWhere('name_hy', $name)
                      ->orWhere('name_ru', $name);
                })
                ->value('id');
        };

        // Import curated items from JSON (attach to demo restaurant)
        $jsonPath = database_path('seeders/data/menu_items.json');
        if (file_exists($jsonPath)) {
            $json = json_decode(file_get_contents($jsonPath), true);
            if (is_array($json)) {
                foreach ($json as $item) {
                    if (!isset($item['category'])) { continue; }
                    $categoryId = $byName($item['category']);
                    if (!$categoryId) { continue; }

                    Menu::updateOrCreate(
                        ['name' => $item['name_en'] ?? $item['name'] ?? 'Unnamed', 'restaurant_id' => $demoRestaurant->id],
                        [
                            'restaurant_id' => $demoRestaurant->id,
                            'name_hy' => $item['name_hy'] ?? null,
                            'name_en' => $item['name_en'] ?? null,
                            'name_ru' => $item['name_ru'] ?? null,
                            'description' => $item['description_en'] ?? ($item['description'] ?? null),
                            'description_hy' => $item['description_hy'] ?? null,
                            'description_en' => $item['description_en'] ?? null,
                            'description_ru' => $item['description_ru'] ?? null,
                            'price' => $item['price'] ?? 0,
                            'category_id' => $categoryId,
                            // Accept either image_path or legacy image_url from JSON
                            'image_path' => $item['image_path'] ?? ($item['image_url'] ?? null),
                            'available' => $item['available'] ?? true,
                        ]
                    );
                }
            }
        }
    }
}
