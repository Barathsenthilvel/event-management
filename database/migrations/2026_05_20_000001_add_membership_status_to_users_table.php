<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'membership_status')) {
                $table->string('membership_status', 32)->default('none')->after('is_approved');
            }
            if (! Schema::hasColumn('users', 'membership_inactive_type')) {
                $table->string('membership_inactive_type', 64)->nullable()->after('membership_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'membership_inactive_type')) {
                $table->dropColumn('membership_inactive_type');
            }
            if (Schema::hasColumn('users', 'membership_status')) {
                $table->dropColumn('membership_status');
            }
        });
    }
};
