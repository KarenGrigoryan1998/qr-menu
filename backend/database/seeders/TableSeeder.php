<?php

namespace Database\Seeders;

use App\Models\Restaurant;
use App\Models\Table;
use Illuminate\Database\Seeder;

class TableSeeder extends Seeder
{
    public function run(): void
    {
        $demoRestaurant = Restaurant::where('slug', 'demo-restaurant')->first();

        for ($i = 1; $i <= 20; $i++) {
            Table::updateOrCreate(
                ['number' => $i, 'restaurant_id' => $demoRestaurant->id],
                [
                    'restaurant_id' => $demoRestaurant->id,
                    'qr_code_url' => "/table/{$i}",
                    'status' => 'free',
                ]
            );
        }
    }
}
