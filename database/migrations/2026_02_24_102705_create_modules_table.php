<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->string('name');              // Module Name
            $table->unsignedBigInteger('menu_id')->nullable()->index(); // Choose Menu
            $table->string('icon')->nullable();  // Icon
            $table->unsignedInteger('sort_no')->default(0); // Sort No
            $table->boolean('is_active')->default(true);    // Status
            $table->timestamps();

            $table->foreign('menu_id')
                  ->references('id')->on('menus')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('modules');
    }
};