<?php

namespace App\Http\Controllers;

use App\Jobs\SendGnatBulkNotificationChunkJob;
use App\Models\GnatNotificationBatch;
use Illuminate\Http\Request;

class GnatNotificationBatchController extends Controller
{
    /** @var array<string, string> */
    private const TYPE_LABELS = [
        SendGnatBulkNotificationChunkJob::TYPE_EVENT_INVITES => 'Event invites',
        SendGnatBulkNotificationChunkJob::TYPE_EVENT_INVITE_REMINDERS => 'Event invite reminders',
        SendGnatBulkNotificationChunkJob::TYPE_MEETING_INVITES => 'Meeting invites',
        SendGnatBulkNotificationChunkJob::TYPE_MEETING_INVITE_REMINDERS => 'Meeting invite reminders',
        SendGnatBulkNotificationChunkJob::TYPE_NOMINATION_ALERTS => 'Nomination alerts',
        SendGnatBulkNotificationChunkJob::TYPE_JOB_POSTING_ALERTS => 'Job posting alerts',
        SendGnatBulkNotificationChunkJob::TYPE_POLLING_LIVE_ALERTS => 'Polling live alerts',
        SendGnatBulkNotificationChunkJob::TYPE_POLLING_RESULTS_ALERTS => 'Polling results alerts',
        'm00_login_otp' => 'Login OTP',
        'm01_registration_successful' => 'Registration Successful',
        'm02_profile_verification_pending' => 'Profile Verification Pending',
        'm03_profile_approved_subscription' => 'Profile Approved Notice',
        'm04_profile_verification_failed' => 'Profile Verification Failed',
        'm05_membership_activated' => 'Membership Activated',
        'm06_renewal_reminder' => 'Membership Renewal Reminder',
        'm07_membership_expired' => 'Membership Expired Notice',
        'm08_membership_cancellation' => 'Membership Cancellation',
        'm09_account_inactive_pending_subscription' => 'Account Inactive Notice',
        'm10_meeting_schedule' => 'Meeting Scheduled',
        'm11_meeting_attendance_confirmed' => 'Meeting Attendance Confirmed',
        'm12_meeting_non_attendance' => 'Meeting Non-Attendance',
        'm13_new_event' => 'New Event Alert',
        'm14_event_interest' => 'Event Interest Receipt',
        'm15_event_participation' => 'Event Participation Check-in',
        'm16_nomination_live' => 'Live Nomination Alert',
        'm17_nomination_submitted' => 'Nomination Submission Receipt',
        'm18_polling_live' => 'Live Polling Alert',
        'm19_polling_response' => 'Polling Response Receipt',
        'm20_polling_results' => 'Polling Results Announcement',
        'm21_job_posting' => 'Job Posting Alert',
        'm22_job_application_confirmation' => 'Job Application Confirmation',
        'm23_job_request_reviewed' => 'Job Request Status Review',
        'm24_job_request_contact' => 'Job Request Contact Update',
        'm25_job_application_selected' => 'Job Selection Notice',
        'm26_donation_confirmation' => 'Donation Confirmation',
        'm27_support_confirmation' => 'Support Ticket Confirmation',
        'm28_event_invite_reminder' => 'Event Invite Reminder',
        'm29_meeting_invite_reminder' => 'Meeting Invite Reminder',
        'm30_meeting_cancelled' => 'Meeting Cancelled',
        'm31_job_application_status' => 'Job Application Status Update',
    ];

    public static function typeLabel(string $type): string
    {
        return self::TYPE_LABELS[$type] ?? str_replace('_', ' ', $type);
    }

    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $status = trim((string) $request->query('status', ''));
        $view = trim((string) $request->query('view', 'batches'));

        if ($view === 'logs') {
            $logsQuery = \App\Models\GnatNotificationDeliveryLog::query()
                ->with(['user:id,name,first_name,last_name,email,mobile', 'batch'])
                ->when($status === 'failed', function ($query) {
                    $query->where(function ($sub) {
                        $sub->where('email_status', 'failed')
                            ->orWhere('sms_status', 'failed')
                            ->orWhere('whatsapp_status', 'failed');
                    });
                })
                ->when($status === 'success', function ($query) {
                    $query->where(function ($sub) {
                        $sub->where('email_status', 'success')
                            ->orWhere('sms_status', 'success')
                            ->orWhere('whatsapp_status', 'success');
                    });
                })
                ->when($status === 'skipped', function ($query) {
                    $query->where(function ($sub) {
                        $sub->where('email_status', 'skipped')
                            ->orWhere('sms_status', 'skipped')
                            ->orWhere('whatsapp_status', 'skipped');
                    });
                })
                ->when($q !== '', function ($query) use ($q) {
                    $query->where(function ($inner) use ($q) {
                        $inner->whereHas('user', function ($uQuery) use ($q) {
                            $uQuery->where('name', 'like', '%'.$q.'%')
                                ->orWhere('first_name', 'like', '%'.$q.'%')
                                ->orWhere('last_name', 'like', '%'.$q.'%')
                                ->orWhere('email', 'like', '%'.$q.'%')
                                ->orWhere('mobile', 'like', '%'.$q.'%');
                        })->orWhereHas('batch', function ($bQuery) use ($q) {
                            $bQuery->where('notification_type', 'like', '%'.$q.'%')
                                ->orWhere('entity_label', 'like', '%'.$q.'%');
                        });
                    });
                })
                ->latest('id')
                ->paginate(50)
                ->withQueryString();

            return view('admin.notification-batches.index', [
                'view' => 'logs',
                'logs' => $logsQuery,
                'batches' => null,
                'q' => $q,
                'status' => $status,
            ]);
        }

        $batches = GnatNotificationBatch::query()
            ->with('initiator:id,name')
            ->when($status !== '', function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($inner) use ($q) {
                    $inner->where('entity_label', 'like', '%'.$q.'%')
                        ->orWhere('notification_type', 'like', '%'.$q.'%');
                });
            })
            ->latest('id')
            ->paginate(25)
            ->withQueryString();

        return view('admin.notification-batches.index', [
            'view' => 'batches',
            'batches' => $batches,
            'logs' => null,
            'q' => $q,
            'status' => $status,
        ]);
    }

    public function show(Request $request, int $id)
    {
        $status = trim((string) $request->query('status', ''));

        $batch = GnatNotificationBatch::query()
            ->with('initiator:id,name')
            ->findOrFail($id);

        $logsQuery = $batch->deliveryLogs()
            ->with('user:id,name,email,mobile');

        if ($status === 'failed') {
            $logsQuery->where(function ($q) {
                $q->where('email_status', 'failed')
                    ->orWhere('sms_status', 'failed')
                    ->orWhere('whatsapp_status', 'failed');
            });
        } elseif ($status !== '') {
            $logsQuery->where(function ($q) use ($status) {
                $q->where('email_status', $status)
                    ->orWhere('sms_status', $status)
                    ->orWhere('whatsapp_status', $status);
            });
        }

        $logs = $logsQuery->latest('id')
            ->paginate(50)
            ->withQueryString();

        return view('admin.notification-batches.show', [
            'batch' => $batch,
            'logs' => $logs,
            'statusFilter' => $status,
        ]);
    }
}
