<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meetings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by_admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->string('title');
            $table->string('meeting_link');
            $table->text('description')->nullable();
            $table->enum('meeting_mode', ['whatsapp', 'teams', 'others', 'direct', 'phone_call'])->default('direct');
            $table->enum('status', ['upcoming', 'live', 'completed', 'cancelled'])->default('upcoming');
            $table->string('cover_image_path')->nullable();
            $table->string('banner_image_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('meeting_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meeting_id')->constrained()->cascadeOnDelete();
            $table->date('meeting_date');
            $table->time('from_time');
            $table->time('to_time');
            $table->timestamps();
        });

        Schema::create('meeting_invites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meeting_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->boolean('notify_whatsapp')->default(false);
            $table->boolean('notify_sms')->default(false);
            $table->boolean('notify_email')->default(false);
            $table->timestamp('invited_at')->nullable();
            $table->timestamp('reminder_sent_at')->nullable();
            $table->timestamps();

            $table->unique(['meeting_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meeting_invites');
        Schema::dropIfExists('meeting_schedules');
        Schema::dropIfExists('meetings');
    }
};

