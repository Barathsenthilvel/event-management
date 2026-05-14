<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gnat_notification_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('initiated_by_admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->string('notification_type', 64);
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->string('entity_label', 255)->nullable();
            $table->unsignedInteger('total_recipients')->default(0);
            $table->unsignedSmallInteger('chunk_size')->default(200);
            $table->unsignedInteger('chunks_total')->default(0);
            $table->unsignedInteger('chunks_finished')->default(0);
            $table->string('status', 32)->default('processing');
            $table->json('meta')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['notification_type', 'created_at']);
            $table->index('status');
        });

        Schema::create('gnat_notification_delivery_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained('gnat_notification_batches')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('email_status', 24)->nullable();
            $table->text('email_error')->nullable();
            $table->string('sms_status', 24)->nullable();
            $table->text('sms_error')->nullable();
            $table->string('whatsapp_status', 24)->nullable();
            $table->text('whatsapp_error')->nullable();
            $table->timestamps();

            $table->unique(['batch_id', 'user_id']);
            $table->index('batch_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gnat_notification_delivery_logs');
        Schema::dropIfExists('gnat_notification_batches');
    }
};
