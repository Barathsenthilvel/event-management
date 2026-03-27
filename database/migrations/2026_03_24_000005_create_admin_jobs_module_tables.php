<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by_admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->string('hospital')->nullable();
            $table->string('title');
            $table->string('code')->unique();
            $table->unsignedInteger('no_of_openings')->default(0);
            $table->boolean('vacancy_permanent')->default(false);
            $table->boolean('vacancy_temporary')->default(false);
            $table->boolean('vacancy_any')->default(false);
            $table->boolean('preference_wfh')->default(false);
            $table->boolean('preference_onsite')->default(false);
            $table->boolean('preference_any')->default(false);
            $table->text('description')->nullable();
            $table->text('key_skills')->nullable();
            $table->boolean('promote_front')->default(false);
            $table->enum('listing_status', ['listed', 'unlisted'])->default('listed');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('admin_job_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained('admin_jobs')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->boolean('notify_whatsapp')->default(false);
            $table->boolean('notify_sms')->default(false);
            $table->boolean('notify_email')->default(false);
            $table->timestamp('alert_sent_at')->nullable();
            $table->timestamps();
            $table->unique(['job_id', 'user_id']);
        });

        Schema::create('admin_job_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained('admin_jobs')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('application_status', ['pending', 'selected', 'not_selected', 'joined', 'not_joined'])->default('pending');
            $table->string('resume_path')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('status_emailed_at')->nullable();
            $table->timestamps();
            $table->unique(['job_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_job_applications');
        Schema::dropIfExists('admin_job_alerts');
        Schema::dropIfExists('admin_jobs');
    }
};

