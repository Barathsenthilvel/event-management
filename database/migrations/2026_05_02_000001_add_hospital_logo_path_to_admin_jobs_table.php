<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admin_jobs', function (Blueprint $table) {
            $table->string('hospital_logo_path')->nullable()->after('hospital');
        });
    }

    public function down(): void
    {
        Schema::table('admin_jobs', function (Blueprint $table) {
            $table->dropColumn('hospital_logo_path');
        });
    }
};
