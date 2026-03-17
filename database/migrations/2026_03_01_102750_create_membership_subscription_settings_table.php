<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('membership_subscription_settings', function (Blueprint $table) {
            $table->id();
            $table->enum('subscription_type', ['New', 'Renewal'])->default('New');
            $table->decimal('membership_fee', 10, 2)->default(0);
            $table->decimal('registration_fee', 10, 2)->default(0);
            $table->boolean('registration_fee_enabled')->default(true);
            $table->integer('grace_period')->default(0); // in days
            $table->json('payment_frequencies')->nullable(); // ['monthly', 'bi_monthly', 'quarterly', 'yearly']
            $table->boolean('discount_based_on_payment')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('membership_subscription_settings');
    }
};
