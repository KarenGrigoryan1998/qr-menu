<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add restaurant_id to tables
        Schema::table('tables', function (Blueprint $table) {
            $table->foreignId('restaurant_id')->after('id')->constrained('restaurants')->cascadeOnDelete();
            $table->dropUnique(['number']); // Remove global unique constraint
            $table->unique(['restaurant_id', 'number']); // Make number unique per restaurant
        });

        // Add restaurant_id to menu_categories
        Schema::table('menu_categories', function (Blueprint $table) {
            $table->foreignId('restaurant_id')->after('id')->constrained('restaurants')->cascadeOnDelete();
            $table->index('restaurant_id');
        });

        // Add restaurant_id to menus
        Schema::table('menus', function (Blueprint $table) {
            $table->foreignId('restaurant_id')->after('id')->constrained('restaurants')->cascadeOnDelete();
            $table->index('restaurant_id');
        });

        // Add restaurant_id to orders
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('restaurant_id')->after('id')->constrained('restaurants')->cascadeOnDelete();
            $table->index('restaurant_id');
        });

        // Add restaurant_id to payments
        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('restaurant_id')->after('id')->constrained('restaurants')->cascadeOnDelete();
            $table->index('restaurant_id');
        });

        // Add restaurant_id to users (for staff assignment)
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('restaurant_id')->after('id')->nullable()->constrained('restaurants')->nullOnDelete();
            $table->index('restaurant_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tables', function (Blueprint $table) {
            $table->dropUnique(['restaurant_id', 'number']);
            $table->dropForeign(['restaurant_id']);
            $table->dropColumn('restaurant_id');
            $table->unique('number');
        });

        Schema::table('menu_categories', function (Blueprint $table) {
            $table->dropForeign(['restaurant_id']);
            $table->dropColumn('restaurant_id');
        });

        Schema::table('menus', function (Blueprint $table) {
            $table->dropForeign(['restaurant_id']);
            $table->dropColumn('restaurant_id');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['restaurant_id']);
            $table->dropColumn('restaurant_id');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['restaurant_id']);
            $table->dropColumn('restaurant_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['restaurant_id']);
            $table->dropColumn('restaurant_id');
        });
    }
};
