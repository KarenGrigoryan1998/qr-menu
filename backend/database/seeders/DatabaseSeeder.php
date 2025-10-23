<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Core data for QrMenu
        $this->call([
            RestaurantSeeder::class,
            RolesSeeder::class,
            AdminUserSeeder::class,
            MenuCategorySeeder::class,
            TableSeeder::class,
            MenuSeeder::class,
        ]);
    }
}
