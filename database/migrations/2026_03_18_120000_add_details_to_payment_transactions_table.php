<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->string('razorpay_signature')->nullable()->after('razorpay_order_id');
            $table->timestamp('paid_at')->nullable()->after('type');
            $table->json('raw_payload')->nullable()->after('paid_at');
        });
    }

    public function down(): void
    {
        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->dropColumn(['razorpay_signature', 'paid_at', 'raw_payload']);
        });
    }
};

