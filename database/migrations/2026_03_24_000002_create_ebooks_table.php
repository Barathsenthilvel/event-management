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
        Schema::create('ebooks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by_admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->string('title');
            $table->string('code')->unique();
            $table->string('hospital')->nullable();
            $table->string('short_description', 500)->nullable();
            $table->text('description')->nullable();
            $table->enum('pricing_type', ['free', 'paid'])->default('free');
            $table->decimal('price', 10, 2)->default(0);
            $table->string('cover_image_path')->nullable();
            $table->string('banner_image_path')->nullable();
            $table->string('material_path')->nullable();
            $table->boolean('promote_front')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ebooks');
    }
};
