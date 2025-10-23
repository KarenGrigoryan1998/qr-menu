<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tables', function (Blueprint $table) {
            $table->string('qr_code_filename')->nullable()->after('qr_code_url');
            $table->unsignedInteger('qr_code_size')->nullable()->default(1024)->after('qr_code_filename');
        });
    }

    public function down(): void
    {
        Schema::table('tables', function (Blueprint $table) {
            $table->dropColumn(['qr_code_filename', 'qr_code_size']);
        });
    }
};
