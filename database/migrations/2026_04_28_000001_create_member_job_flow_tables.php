<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member_saved_jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained('admin_jobs')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['job_id', 'user_id']);
        });

        Schema::create('member_job_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('name');
            $table->string('mobile', 40)->nullable();
            $table->string('email');
            $table->string('qualification', 255)->nullable();
            $table->string('position_looking_for', 255)->nullable();
            $table->string('experience', 255)->nullable();
            $table->text('details')->nullable();
            $table->string('resume_path')->nullable();
            $table->enum('status', ['new', 'reviewed', 'contacted', 'closed'])->default('new');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_job_requests');
        Schema::dropIfExists('member_saved_jobs');
    }
};

