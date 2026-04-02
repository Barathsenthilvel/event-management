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

        if ($driver !== 'mysql' && $driver !== 'pgsql') {
            return;
        }

        Schema::disableForeignKeyConstraints();

        if ($driver === 'mysql') {
            $fks = DB::select(
                "SELECT CONSTRAINT_NAME AS name FROM information_schema.TABLE_CONSTRAINTS
                 WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'event_interests' AND CONSTRAINT_TYPE = 'FOREIGN KEY'"
            );
            foreach ($fks as $fk) {
                DB::statement('ALTER TABLE event_interests DROP FOREIGN KEY `' . $fk->name . '`');
            }

            $indexes = DB::select(
                "SHOW INDEX FROM event_interests WHERE Key_name = 'event_interests_event_id_user_id_unique'"
            );
            if (count($indexes) > 0) {
                DB::statement('ALTER TABLE event_interests DROP INDEX event_interests_event_id_user_id_unique');
            }

            DB::statement('ALTER TABLE event_interests MODIFY user_id BIGINT UNSIGNED NULL');

            $hasPair = DB::select(
                "SHOW INDEX FROM event_interests WHERE Key_name = 'event_interests_event_id_email_unique'"
            );
            if (count($hasPair) === 0) {
                Schema::table('event_interests', function (Blueprint $table) {
                    $table->unique(['event_id', 'email']);
                });
            }

            $fksAfter = DB::select(
                "SELECT CONSTRAINT_NAME AS name FROM information_schema.TABLE_CONSTRAINTS
                 WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'event_interests' AND CONSTRAINT_TYPE = 'FOREIGN KEY' AND CONSTRAINT_NAME LIKE '%user_id%'"
            );
            if (count($fksAfter) === 0) {
                Schema::table('event_interests', function (Blueprint $table) {
                    $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
                });
            }
        } elseif ($driver === 'pgsql') {
            Schema::table('event_interests', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
            });
            Schema::table('event_interests', function (Blueprint $table) {
                $table->dropUnique(['event_id', 'user_id']);
            });
            DB::statement('ALTER TABLE event_interests ALTER COLUMN user_id DROP NOT NULL');
            Schema::table('event_interests', function (Blueprint $table) {
                $table->unique(['event_id', 'email']);
                $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            });
        }

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::table('event_interests', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropUnique(['event_id', 'email']);
        });

        DB::table('event_interests')->whereNull('user_id')->delete();

        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE event_interests MODIFY user_id BIGINT UNSIGNED NOT NULL');
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE event_interests ALTER COLUMN user_id SET NOT NULL');
        }

        Schema::table('event_interests', function (Blueprint $table) {
            $table->unique(['event_id', 'user_id']);
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::enableForeignKeyConstraints();
    }
};
