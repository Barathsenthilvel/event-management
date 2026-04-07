<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('polling_position_candidates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('polling_position_id')->constrained('polling_positions')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['polling_position_id', 'user_id']);
        });

        $rows = DB::table('polling_positions')
            ->whereNotNull('member_user_id')
            ->get(['id', 'member_user_id']);

        foreach ($rows as $row) {
            DB::table('polling_position_candidates')->insert([
                'polling_position_id' => $row->id,
                'user_id' => $row->member_user_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        Schema::table('polling_positions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('member_user_id');
        });
    }

    public function down(): void
    {
        Schema::table('polling_positions', function (Blueprint $table) {
            $table->foreignId('member_user_id')->nullable()->after('position')->constrained('users')->nullOnDelete();
        });

        $rows = DB::table('polling_position_candidates')->get(['polling_position_id', 'user_id']);
        foreach ($rows as $row) {
            DB::table('polling_positions')
                ->where('id', $row->polling_position_id)
                ->update(['member_user_id' => $row->user_id]);
        }

        Schema::dropIfExists('polling_position_candidates');
    }
};
