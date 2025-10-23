<?php

namespace Database\Seeders;

use App\Models\MenuCategory;
use App\Models\Restaurant;
use Illuminate\Database\Seeder;

class MenuCategorySeeder extends Seeder
{
    public function run(): void
    {
        $demoRestaurant = Restaurant::where('slug', 'demo-restaurant')->first();

        $jsonPath = database_path('seeders/data/menu_categories.json');
        $cats = [];
        if (file_exists($jsonPath)) {
            $decoded = json_decode(file_get_contents($jsonPath), true);
            if (is_array($decoded)) {
                $cats = $decoded;
            }
        }

        foreach ($cats as $c) {
            MenuCategory::updateOrCreate(
                ['name' => $c['name'], 'restaurant_id' => $demoRestaurant->id],
                [
                    'restaurant_id' => $demoRestaurant->id,
                    'name_hy' => $c['name_hy'],
                    'name_en' => $c['name_en'],
                    'name_ru' => $c['name_ru'],
                    'image_path' => $c['image_url'] ?? ($c['image_path'] ?? null),
                ]
            );
        }
    }
}
