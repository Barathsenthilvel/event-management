<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('event_invites', function (Blueprint $table) {
            $table->boolean('has_confirmed_interest')->default(false)->after('invited_at');
        });

        // Existing rows were real portal registrations or admin-set attendance; treat as confirmed.
        DB::table('event_invites')->update(['has_confirmed_interest' => true]);
    }

    public function down(): void
    {
        Schema::table('event_invites', function (Blueprint $table) {
            $table->dropColumn('has_confirmed_interest');
        });
    }
};
