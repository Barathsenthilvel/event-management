<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->date('dob')->nullable()->after('last_name');
            $table->string('gender', 30)->nullable()->after('dob');
            $table->string('qualification', 120)->nullable()->after('gender');
            $table->string('blood_group', 30)->nullable()->after('qualification');

            $table->string('rnrm_number_with_date', 120)->nullable()->after('blood_group');
            $table->string('college_name', 190)->nullable()->after('rnrm_number_with_date');

            $table->string('door_no', 80)->nullable()->after('college_name');
            $table->string('locality_area', 190)->nullable()->after('door_no');
            $table->string('state', 120)->nullable()->after('locality_area');
            $table->string('pin_code', 20)->nullable()->after('state');
            $table->string('council_state', 120)->nullable()->after('pin_code');

            $table->string('currently_working', 190)->nullable()->after('council_state');

            $table->string('educational_certificate_path')->nullable()->after('currently_working');
            $table->string('aadhar_card_path')->nullable()->after('educational_certificate_path');
            $table->string('passport_photo_path')->nullable()->after('aadhar_card_path');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'dob',
                'gender',
                'qualification',
                'blood_group',
                'rnrm_number_with_date',
                'college_name',
                'door_no',
                'locality_area',
                'state',
                'pin_code',
                'council_state',
                'currently_working',
                'educational_certificate_path',
                'aadhar_card_path',
                'passport_photo_path',
            ]);
        });
    }
};

