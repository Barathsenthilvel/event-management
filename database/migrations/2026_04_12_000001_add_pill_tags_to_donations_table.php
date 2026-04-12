<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->string('pill_tag_1', 64)->default('Donation')->after('description');
            $table->string('pill_tag_2', 64)->default('Charity')->after('pill_tag_1');
        });
    }

    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->dropColumn(['pill_tag_1', 'pill_tag_2']);
        });
    }
};
