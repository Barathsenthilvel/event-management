<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('member_subscriptions', function (Blueprint $table) {
            $table->timestamp('renewal_reminder_sent_at')->nullable()->after('last_razorpay_payment_id');
            $table->timestamp('expiry_notification_sent_at')->nullable()->after('renewal_reminder_sent_at');
        });

        if (Schema::hasTable('pollings')) {
            Schema::table('pollings', function (Blueprint $table) {
                if (! Schema::hasColumn('pollings', 'live_alert_sent_at')) {
                    $table->timestamp('live_alert_sent_at')->nullable()->after('updated_at');
                }
                if (! Schema::hasColumn('pollings', 'results_mail_sent_at')) {
                    $table->timestamp('results_mail_sent_at')->nullable()->after('live_alert_sent_at');
                }
            });
        }

        if (Schema::hasTable('events')) {
            Schema::table('events', function (Blueprint $table) {
                if (! Schema::hasColumn('events', 'member_notification_sent_at')) {
                    $table->timestamp('member_notification_sent_at')->nullable()->after('updated_at');
                }
            });
        }

        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'gnat_inactive_notice_sent_at')) {
                $table->timestamp('gnat_inactive_notice_sent_at')->nullable()->after('remember_token');
            }
        });
    }

    public function down(): void
    {
        Schema::table('member_subscriptions', function (Blueprint $table) {
            $table->dropColumn(['renewal_reminder_sent_at', 'expiry_notification_sent_at']);
        });

        if (Schema::hasTable('pollings')) {
            Schema::table('pollings', function (Blueprint $table) {
                $cols = [];
                if (Schema::hasColumn('pollings', 'live_alert_sent_at')) {
                    $cols[] = 'live_alert_sent_at';
                }
                if (Schema::hasColumn('pollings', 'results_mail_sent_at')) {
                    $cols[] = 'results_mail_sent_at';
                }
                if ($cols !== []) {
                    $table->dropColumn($cols);
                }
            });
        }

        if (Schema::hasTable('events')) {
            Schema::table('events', function (Blueprint $table) {
                if (Schema::hasColumn('events', 'member_notification_sent_at')) {
                    $table->dropColumn('member_notification_sent_at');
                }
            });
        }

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'gnat_inactive_notice_sent_at')) {
                $table->dropColumn('gnat_inactive_notice_sent_at');
            }
        });
    }
};
