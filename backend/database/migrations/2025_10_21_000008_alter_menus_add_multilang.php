<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->string('name_hy')->nullable()->after('name');
            $table->string('name_en')->nullable()->after('name_hy');
            $table->string('name_ru')->nullable()->after('name_en');
            $table->text('description_hy')->nullable()->after('description');
            $table->text('description_en')->nullable()->after('description_hy');
            $table->text('description_ru')->nullable()->after('description_en');
            $table->string('image_path')->nullable();
            $table->string('filename')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->dropColumn(['name_hy','name_en','name_ru','description_hy','description_en','description_ru', 'image_path', 'filename']);
        });
    }
};
