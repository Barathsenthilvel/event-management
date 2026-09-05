<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_transactions', function (Blueprint $table) {
            if (! Schema::hasColumn('payment_transactions', 'razorpay_payment_link_id')) {
                $table->string('razorpay_payment_link_id')->nullable()->index()->after('razorpay_order_id');
            }
            if (! Schema::hasColumn('payment_transactions', 'reference_id')) {
                $table->string('reference_id')->nullable()->index()->after('razorpay_payment_link_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('payment_transactions', function (Blueprint $table) {
            if (Schema::hasColumn('payment_transactions', 'razorpay_payment_link_id')) {
                $table->dropColumn('razorpay_payment_link_id');
            }
            if (Schema::hasColumn('payment_transactions', 'reference_id')) {
                $table->dropColumn('reference_id');
            }
        });
    }
};
