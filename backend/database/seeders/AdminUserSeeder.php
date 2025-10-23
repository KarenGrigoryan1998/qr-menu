<?php

namespace Database\Seeders;

use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $demoRestaurant = Restaurant::where('slug', 'demo-restaurant')->first();

        // Create Super Admin (not tied to any restaurant)
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@qrmenu.com',
            'password' => Hash::make('password'),
            'restaurant_id' => null,
        ]);
        $superAdmin->assignRole('super-admin');

        // Create Owner for Demo Restaurant
        $owner = User::create([
            'name' => 'Demo Owner',
            'email' => 'owner@demo.com',
            'password' => Hash::make('password'),
            'restaurant_id' => $demoRestaurant->id,
        ]);
        $owner->assignRole('owner');

        // Create Manager for Demo Restaurant
        $manager = User::create([
            'name' => 'Demo Manager',
            'email' => 'manager@demo.com',
            'password' => Hash::make('password'),
            'restaurant_id' => $demoRestaurant->id,
        ]);
        $manager->assignRole('manager');

        // Create Waiter for Demo Restaurant
        $waiter = User::create([
            'name' => 'Demo Waiter',
            'email' => 'waiter@demo.com',
            'password' => Hash::make('password'),
            'restaurant_id' => $demoRestaurant->id,
        ]);
        $waiter->assignRole('waiter');
    }
}
