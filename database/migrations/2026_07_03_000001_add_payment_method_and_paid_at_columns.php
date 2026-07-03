<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('donation_payments', function (Blueprint $table) {
            if (! Schema::hasColumn('donation_payments', 'payment_method')) {
                $table->string('payment_method', 50)->nullable()->after('payment_gateway');
            }
            if (! Schema::hasColumn('donation_payments', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('status');
            }
        });

        Schema::table('payment_transactions', function (Blueprint $table) {
            if (! Schema::hasColumn('payment_transactions', 'payment_method')) {
                $table->string('payment_method', 50)->nullable()->after('razorpay_signature');
            }
        });

        // Backfill paid_at so the dashboard donation chart includes historical successes.
        if (Schema::hasColumn('donation_payments', 'paid_at')) {
            DB::table('donation_payments')
                ->where('status', 'successful')
                ->whereNull('paid_at')
                ->update([
                    'paid_at' => DB::raw('COALESCE(updated_at, created_at)'),
                ]);
        }
    }

    public function down(): void
    {
        Schema::table('donation_payments', function (Blueprint $table) {
            if (Schema::hasColumn('donation_payments', 'payment_method')) {
                $table->dropColumn('payment_method');
            }
            if (Schema::hasColumn('donation_payments', 'paid_at')) {
                $table->dropColumn('paid_at');
            }
        });

        Schema::table('payment_transactions', function (Blueprint $table) {
            if (Schema::hasColumn('payment_transactions', 'payment_method')) {
                $table->dropColumn('payment_method');
            }
        });
    }
};
