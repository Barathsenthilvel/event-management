<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('home_gallery_items', function (Blueprint $table) {
            $table->boolean('is_category_primary')->default(false)->after('category_key');
        });
    }

    public function down(): void
    {
        Schema::table('home_gallery_items', function (Blueprint $table) {
            $table->dropColumn('is_category_primary');
        });
    }
};
