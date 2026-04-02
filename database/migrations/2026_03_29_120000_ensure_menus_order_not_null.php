<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('menus')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement('UPDATE `menus` SET `order` = 0 WHERE `order` IS NULL');
            DB::statement('ALTER TABLE `menus` MODIFY `order` INT UNSIGNED NOT NULL DEFAULT 0');
        }
    }

    public function down(): void
    {
        // Intentionally left blank: reverting NOT NULL could break existing rows.
    }
};
