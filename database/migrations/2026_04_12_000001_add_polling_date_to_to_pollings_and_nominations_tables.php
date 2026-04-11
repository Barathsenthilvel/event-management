<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pollings', function (Blueprint $table) {
            $table->date('polling_date_to')->nullable()->after('polling_date');
        });

        Schema::table('nominations', function (Blueprint $table) {
            $table->date('polling_date_to')->nullable()->after('polling_date');
        });
    }

    public function down(): void
    {
        Schema::table('pollings', function (Blueprint $table) {
            $table->dropColumn('polling_date_to');
        });

        Schema::table('nominations', function (Blueprint $table) {
            $table->dropColumn('polling_date_to');
        });
    }
};
