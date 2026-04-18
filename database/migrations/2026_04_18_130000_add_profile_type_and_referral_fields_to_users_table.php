<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('profile_type', ['registered_nurse', 'student_nurse', 'volunteer'])
                ->nullable()
                ->after('blood_group');

            $table->string('student_id', 120)->nullable()->after('rnrm_number_with_date');

            $table->foreignId('referred_by_user_id')
                ->nullable()
                ->after('designation_id')
                ->constrained('users')
                ->nullOnDelete();

            $table->string('rnrm_certificate_path')->nullable()->after('educational_certificate_path');
            $table->string('student_id_card_path')->nullable()->after('rnrm_certificate_path');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('referred_by_user_id');
            $table->dropColumn([
                'profile_type',
                'student_id',
                'rnrm_certificate_path',
                'student_id_card_path',
            ]);
        });
    }
};

