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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by_admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('venue')->nullable();
            $table->enum('seat_mode', ['unlimited', 'limited'])->default('unlimited');
            $table->unsignedInteger('seat_limit')->nullable();
            $table->unsignedInteger('interested_count')->default(0);
            $table->boolean('promote_front')->default(false);
            $table->enum('status', ['upcoming', 'live', 'completed', 'cancelled'])->default('upcoming');
            $table->boolean('is_active')->default(true);
            $table->string('cover_image_path')->nullable();
            $table->string('banner_image_path')->nullable();
            $table->string('template_pdf_path')->nullable();
            $table->timestamps();
        });

        Schema::create('event_dates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->date('event_date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->timestamps();
        });

        Schema::create('event_invites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->boolean('notify_whatsapp')->default(false);
            $table->boolean('notify_sms')->default(false);
            $table->boolean('notify_email')->default(false);
            $table->enum('participation_status', ['interested', 'participated', 'not_participated'])->default('interested');
            $table->timestamp('invited_at')->nullable();
            $table->timestamp('reminder_sent_at')->nullable();
            $table->timestamps();

            $table->unique(['event_id', 'user_id']);
        });

        Schema::create('event_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->string('photo_path');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_photos');
        Schema::dropIfExists('event_invites');
        Schema::dropIfExists('event_dates');
        Schema::dropIfExists('events');
    }
};
