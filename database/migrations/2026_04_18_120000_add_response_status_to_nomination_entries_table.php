<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nomination_entries', function (Blueprint $table) {
            $table->enum('response_status', ['interested', 'not_interested'])
                ->default('interested')
                ->after('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('nomination_entries', function (Blueprint $table) {
            $table->dropColumn('response_status');
        });
    }
};
