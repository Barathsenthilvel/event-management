<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Drop modules table – Module functionality removed; Menu Management only for admin sidebar.
     */
    public function up(): void
    {
        Schema::dropIfExists('modules');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('route_name')->nullable();
            $table->string('icon')->nullable();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable()->index();
            $table->unsignedInteger('sort_no')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('parent_id')
                ->references('id')->on('modules')
                ->onDelete('cascade');
        });
    }
};
