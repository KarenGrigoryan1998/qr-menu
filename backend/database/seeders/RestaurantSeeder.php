<?php

namespace Database\Seeders;

use App\Models\Restaurant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RestaurantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Restaurant::create([
            'name' => 'Demo Restaurant',
            'slug' => 'demo-restaurant',
            'settings' => [
                'currency' => 'AMD',
                'timezone' => 'Asia/Yerevan',
                'languages' => ['hy', 'en', 'ru'],
                'default_language' => 'hy',
            ],
        ]);
    }
}
