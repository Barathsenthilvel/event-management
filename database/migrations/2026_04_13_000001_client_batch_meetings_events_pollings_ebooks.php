<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE meetings MODIFY meeting_link VARCHAR(255) NULL');
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE meetings ALTER COLUMN meeting_link DROP NOT NULL');
        }
        // SQLite: column remains NOT NULL until manual fix; local dev often uses MySQL.

        if (! Schema::hasColumn('event_interests', 'participation_status')) {
            Schema::table('event_interests', function (Blueprint $table) {
                $table->enum('participation_status', ['interested', 'participated', 'not_participated'])
                    ->default('interested');
            });
        }

        if (! Schema::hasColumn('pollings', 'results_visible_to_members')) {
            Schema::table('pollings', function (Blueprint $table) {
                $table->boolean('results_visible_to_members')->default(false);
            });
        }

        if (! Schema::hasColumn('polling_positions', 'winner_user_id')) {
            Schema::table('polling_positions', function (Blueprint $table) {
                $table->foreignId('winner_user_id')->nullable()->constrained('users')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('polling_positions', 'winner_user_id')) {
            Schema::table('polling_positions', function (Blueprint $table) {
                $table->dropConstrainedForeignId('winner_user_id');
            });
        }

        if (Schema::hasColumn('pollings', 'results_visible_to_members')) {
            Schema::table('pollings', function (Blueprint $table) {
                $table->dropColumn('results_visible_to_members');
            });
        }

        if (Schema::hasColumn('event_interests', 'participation_status')) {
            Schema::table('event_interests', function (Blueprint $table) {
                $table->dropColumn('participation_status');
            });
        }

        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE meetings MODIFY meeting_link VARCHAR(255) NOT NULL');
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE meetings ALTER COLUMN meeting_link SET NOT NULL');
        }
    }
};
