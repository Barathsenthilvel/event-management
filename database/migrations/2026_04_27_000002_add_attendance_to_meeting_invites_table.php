<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('meeting_invites', function (Blueprint $table) {
            $table->string('participation_status', 32)->default('interested')->after('reminder_sent_at');
            $table->timestamp('attended_at')->nullable()->after('participation_status');
        });
    }

    public function down(): void
    {
        Schema::table('meeting_invites', function (Blueprint $table) {
            $table->dropColumn(['participation_status', 'attended_at']);
        });
    }
};

