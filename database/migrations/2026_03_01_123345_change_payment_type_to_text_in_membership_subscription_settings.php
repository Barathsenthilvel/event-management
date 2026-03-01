<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('membership_subscription_settings', function (Blueprint $table) {
            // Change to text so it can store a JSON array of payment types
            $table->text('payment_type')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('membership_subscription_settings', function (Blueprint $table) {
            $table->string('payment_type')->nullable()->change();
        });
    }
};
