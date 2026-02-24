<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('modules', function (Blueprint $table) {
            if (!Schema::hasColumn('modules', 'route_name')) {
                $table->string('route_name')->nullable()->after('name');
            }
            if (!Schema::hasColumn('modules', 'description')) {
                $table->text('description')->nullable()->after('icon');
            }
            if (!Schema::hasColumn('modules', 'parent_id')) {
                $table->unsignedBigInteger('parent_id')->nullable()->index()->after('description');
                $table->foreign('parent_id')
                    ->references('id')->on('modules')
                    ->onDelete('cascade');
            }
        });
    }

    public function down(): void
    {
        Schema::table('modules', function (Blueprint $table) {
            if (Schema::hasColumn('modules', 'parent_id')) {
                $table->dropForeign(['parent_id']);
                $table->dropColumn('parent_id');
            }
            if (Schema::hasColumn('modules', 'description')) {
                $table->dropColumn('description');
            }
            if (Schema::hasColumn('modules', 'route_name')) {
                $table->dropColumn('route_name');
            }
        });
    }
};

