<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('membership_subscription_setting_id')
                ->constrained('membership_subscription_settings')
                ->cascadeOnDelete();

            $table->string('subscription_type')->nullable(); // New / Renewal (copied for history)
            $table->string('payment_type')->nullable(); // monthly/quarterly/etc (copied for history)

            $table->decimal('amount', 10, 2)->default(0);
            $table->string('currency', 10)->default('INR');

            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('status')->default('active'); // active/expired/cancelled

            $table->string('razorpay_order_id')->nullable()->index();
            $table->string('last_razorpay_payment_id')->nullable()->index();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_subscriptions');
    }
};

