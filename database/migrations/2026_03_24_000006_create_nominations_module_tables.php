<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nominations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by_admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->string('title');
            $table->text('terms')->nullable();
            $table->date('polling_date');
            $table->time('polling_from');
            $table->time('polling_to');
            $table->string('cover_image_path')->nullable();
            $table->string('banner_image_path')->nullable();
            $table->enum('status', ['draft', 'active', 'closed', 'cancelled'])->default('draft');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('nomination_positions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nomination_id')->constrained()->cascadeOnDelete();
            $table->string('position');
            $table->foreignId('member_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('nomination_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nomination_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->boolean('notify_whatsapp')->default(false);
            $table->boolean('notify_sms')->default(false);
            $table->boolean('notify_email')->default(false);
            $table->timestamp('alert_sent_at')->nullable();
            $table->timestamps();
            $table->unique(['nomination_id', 'user_id']);
        });

        Schema::create('nomination_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nomination_id')->constrained()->cascadeOnDelete();
            $table->foreignId('position_id')->constrained('nomination_positions')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
            $table->unique(['nomination_id', 'position_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nomination_entries');
        Schema::dropIfExists('nomination_alerts');
        Schema::dropIfExists('nomination_positions');
        Schema::dropIfExists('nominations');
    }
};

