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
        Schema::table('membership_subscription_settings', function (Blueprint $table) {
            // Remove old columns
            $table->dropColumn(['payment_frequencies', 'grace_period']);
            
            // Add payment option columns with amount and grace period for each
            $table->decimal('monthly_amount', 10, 2)->nullable()->after('registration_fee_enabled');
            $table->integer('monthly_grace_period')->default(0)->after('monthly_amount');
            
            $table->decimal('bi_monthly_amount', 10, 2)->nullable()->after('monthly_grace_period');
            $table->integer('bi_monthly_grace_period')->default(0)->after('bi_monthly_amount');
            
            $table->decimal('quarterly_amount', 10, 2)->nullable()->after('bi_monthly_grace_period');
            $table->integer('quarterly_grace_period')->default(0)->after('quarterly_amount');
            
            $table->decimal('yearly_amount', 10, 2)->nullable()->after('quarterly_grace_period');
            $table->integer('yearly_grace_period')->default(0)->after('yearly_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('membership_subscription_settings', function (Blueprint $table) {
            // Remove new columns
            $table->dropColumn([
                'monthly_amount', 'monthly_grace_period',
                'bi_monthly_amount', 'bi_monthly_grace_period',
                'quarterly_amount', 'quarterly_grace_period',
                'yearly_amount', 'yearly_grace_period'
            ]);
            
            // Restore old columns
            $table->json('payment_frequencies')->nullable();
            $table->integer('grace_period')->default(0);
        });
    }
};
