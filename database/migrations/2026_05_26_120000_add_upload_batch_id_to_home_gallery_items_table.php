<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('home_gallery_items', function (Blueprint $table) {
            $table->uuid('upload_batch_id')->nullable()->after('created_by_admin_id')->index();
        });
    }

    public function down(): void
    {
        Schema::table('home_gallery_items', function (Blueprint $table) {
            $table->dropColumn('upload_batch_id');
        });
    }
};
