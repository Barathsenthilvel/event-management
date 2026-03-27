<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pollings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by_admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->string('title');
            $table->date('polling_date');
            $table->time('polling_from');
            $table->time('polling_to');
            $table->string('cover_image_path')->nullable();
            $table->string('banner_image_path')->nullable();
            $table->boolean('promote_front')->default(false);
            $table->enum('publish_status', ['na', 'pending', 'published'])->default('na');
            $table->enum('polling_status', ['live', 'ends'])->default('ends');
            $table->boolean('show_stats')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('polling_positions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('polling_id')->constrained('pollings')->cascadeOnDelete();
            $table->string('position');
            $table->foreignId('member_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('polling_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('polling_id')->constrained('pollings')->cascadeOnDelete();
            $table->foreignId('position_id')->constrained('polling_positions')->cascadeOnDelete();
            $table->foreignId('candidate_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('voter_user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('voted_at')->nullable();
            $table->timestamps();
            $table->unique(['polling_id', 'position_id', 'voter_user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('polling_votes');
        Schema::dropIfExists('polling_positions');
        Schema::dropIfExists('pollings');
    }
};

