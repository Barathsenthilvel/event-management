<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('membership_subscription_settings', function (Blueprint $table) {
            // Add direct payment_type and grace_period columns
            $table->string('payment_type')->nullable()->after('registration_fee_enabled');
            $table->integer('grace_period')->default(0)->after('payment_type');

            // Drop all per-type amount and grace_period columns (no longer used)
            $table->dropColumn([
                'monthly_amount',
                'monthly_grace_period',
                'bi_monthly_amount',
                'bi_monthly_grace_period',
                'quarterly_amount',
                'quarterly_grace_period',
                'yearly_amount',
                'yearly_grace_period',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('membership_subscription_settings', function (Blueprint $table) {
            $table->dropColumn(['payment_type', 'grace_period']);

            $table->decimal('monthly_amount', 10, 2)->nullable();
            $table->integer('monthly_grace_period')->default(0);
            $table->decimal('bi_monthly_amount', 10, 2)->nullable();
            $table->integer('bi_monthly_grace_period')->default(0);
            $table->decimal('quarterly_amount', 10, 2)->nullable();
            $table->integer('quarterly_grace_period')->default(0);
            $table->decimal('yearly_amount', 10, 2)->nullable();
            $table->integer('yearly_grace_period')->default(0);
        });
    }
};
