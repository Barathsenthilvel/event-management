<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('default_settings', function (Blueprint $table) {
            $table->id();
            $table->string('country_code', 3)->index();
            $table->string('country_name');
            $table->string('currency_code', 10);
            $table->string('currency_name')->nullable();
            $table->string('time_format', 50)->default('24h');
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('default_settings');
    }
};
