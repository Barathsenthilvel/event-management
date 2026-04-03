<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'designation_id')) {
                $table->foreignId('designation_id')
                    ->nullable()
                    ->after('is_approved')
                    ->constrained('designations')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'designation_id')) {
                $table->dropForeign(['designation_id']);
            }
        });
    }
};
