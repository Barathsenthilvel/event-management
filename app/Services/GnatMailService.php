<?php

namespace App\Services;

use App\Mail\GnatAdminNotification;
use App\Mail\GnatMemberNotification;
use App\Models\AdminJob;
use App\Models\AdminJobAlert;
use App\Models\AdminJobApplication;
use App\Models\DonationPayment;
use App\Models\Event;
use App\Models\EventInvite;
use App\Models\GnatNotificationDeliveryLog;
use App\Models\Meeting;
use App\Models\MeetingInvite;
use App\Models\MemberJobRequest;
use App\Models\MemberSubscription;
use App\Models\Nomination;
use App\Models\NominationAlert;
use App\Models\NominationPosition;
use App\Models\PaymentTransaction;
use App\Models\Polling;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class GnatMailService
{
    private function sms(): GnatSmsService
    {
        return app(GnatSmsService::class);
    }

    /** @var array<string, string> */
    private const MEMBER_SUBJECTS = [
        'm01_registration_successful' => 'GNAT Registration Successful – Complete Your Profile',
        'm02_profile_verification_pending' => 'GNAT Profile Submission Under Verification',
        'm03_profile_approved_subscription' => 'GNAT Profile Approved – Membership Subscription Required',
        'm04_profile_verification_failed' => 'GNAT Profile Verification Update',
        'm05_membership_activated' => 'GNAT Membership Activated Successfully',
        'm06_renewal_reminder' => 'GNAT Membership Renewal Reminder',
        'm07_membership_expired' => 'GNAT Membership Expired Notification',
        'm08_membership_cancellation' => 'GNAT Membership Cancellation Confirmation',
        'm09_account_inactive_pending_subscription' => 'GNAT Account Inactive Due to Pending Subscription',
        'm10_meeting_schedule' => 'GNAT Meeting Schedule Notification',
        'm11_meeting_attendance_confirmed' => 'GNAT Meeting Attendance Confirmation',
        'm12_meeting_non_attendance' => 'Meeting Non-Attendance Acknowledgment',
        'm13_new_event' => 'GNAT New Event Update Notification',
        'm14_event_interest' => 'GNAT Event Interest Confirmation',
        'm15_event_participation' => 'GNAT Event Participation Confirmation',
        'm16_nomination_live' => 'GNAT Live Nomination Alert',
        'm17_nomination_submitted' => 'GNAT Nomination Submission Confirmation',
        'm18_polling_live' => 'GNAT Live Polling Alert',
        'm19_polling_response' => 'GNAT Polling Response Confirmation',
        'm20_polling_results' => 'GNAT Polling Result Notification',
        'm21_job_posting' => 'GNAT Job Posting Notification',
        'm22_job_application_confirmation' => 'GNAT Job Application Confirmation',
        'm23_job_request_reviewed' => 'GNAT Job Request Status Update',
        'm24_job_request_contact' => 'GNAT Job Request Status Update',
        'm25_job_application_selected' => 'GNAT Job Communication Update',
        'm26_donation_confirmation' => 'GNAT Donation Payment Confirmation',
        'm27_support_confirmation' => 'GNAT Support Request Confirmation',
        'm28_event_invite_reminder' => 'GNAT Event Reminder',
        'm29_meeting_invite_reminder' => 'GNAT Meeting Reminder',
    ];

    /** @var array<string, string> */
    private const ADMIN_SUBJECTS = [
        'a01_profile_submitted' => 'New GNAT Profile Submitted – Verification Required',
        'a02_subscription_payment' => 'GNAT Membership Subscription Payment Received',
        'a03_renewal_reminder_sent' => 'GNAT Membership Renewal Reminder',
        'a04_membership_expired' => 'GNAT Membership Expired Notification',
        'a05_account_inactive' => 'GNAT Account Marked Inactive Due to Pending Subscription',
        'a06_cancellation' => 'GNAT Membership Cancellation Request',
        'a07_meeting_attendance_confirmed' => 'GNAT Meeting Attendance Confirmed',
        'a08_meeting_non_attendance' => 'GNAT Meeting Non-Attendance Recorded',
        'a09_event_interest' => 'GNAT Event Interest Received',
        'a10_nomination_received' => 'GNAT Nomination Received',
        'a11_poll_response' => 'GNAT Poll Response Received',
        'a12_job_application' => 'New GNAT Job Application Received',
        'a13_donation_received' => 'GNAT Donation Payment Received',
        'a14_support_request' => 'GNAT Support Request Received',
    ];

    public function memberPortalUrl(): string
    {
        $override = config('gnat_mail.portal_member_url');

        return $override ? rtrim((string) $override, '/') : url('/member/dashboard');
    }

    /**
     * @return list<string>
     */
    public function adminRecipients(): array
    {
        $configured = array_values(array_filter(config('gnat_mail.admin_addresses', [])));
        if ($configured !== []) {
            return $configured;
        }

        $fallback = config('homepage.contact_form_to') ?: config('homepage.contact.email');
        if ($fallback) {
            return [trim((string) $fallback)];
        }

        $from = config('mail.from.address');

        return $from ? [trim((string) $from)] : [];
    }

    public function memberDisplayName(?User $user): string
    {
        if (! $user) {
            return 'Member';
        }

        $name = trim((string) ($user->name ?? ''));
        if ($name !== '') {
            return $name;
        }

        return trim(trim((string) ($user->first_name ?? '')).' '.trim((string) ($user->last_name ?? ''))) ?: 'Member';
    }

    /**
     * @param  array<string, mixed>  $viewData
     */
    public function sendMember(?string $email, string $templateKey, array $viewData = []): void
    {
        if ($email === null || trim($email) === '') {
            return;
        }

        $subject = self::MEMBER_SUBJECTS[$templateKey] ?? 'GNAT Association';
        $viewData['portalUrl'] = $viewData['portalUrl'] ?? $this->memberPortalUrl();

        $this->safeSend(function () use ($email, $templateKey, $subject, $viewData) {
            Mail::to($email)->send(new GnatMemberNotification($templateKey, $subject, $viewData));
        });
    }

    /**
     * @param  array<string, mixed>  $viewData
     * @param  list<\Illuminate\Mail\Mailables\Address>  $replyTo
     */
    public function sendAdmin(string $templateKey, array $viewData = [], array $replyTo = []): void
    {
        $recipients = $this->adminRecipients();
        if ($recipients === []) {
            return;
        }

        $subject = self::ADMIN_SUBJECTS[$templateKey] ?? 'GNAT Admin Notification';

        foreach ($recipients as $address) {
            $this->safeSend(function () use ($address, $templateKey, $subject, $viewData, $replyTo) {
                Mail::to($address)->send(new GnatAdminNotification($templateKey, $subject, $viewData, $replyTo));
            });
        }
    }

    public function sendRegistrationSuccessful(User $user): void
    {
        $name = $this->memberDisplayName($user);
        $this->sendMember($user->email, 'm01_registration_successful', [
            'memberName' => $name,
            'heroHeadline' => 'Welcome to GNAT',
            'heroSubtext' => 'Your registration is complete. Complete your profile to unlock member services.',
            'showPortalCta' => true,
        ]);
        $this->sms()->registrationComplete($user->mobile, $name);
    }

    public function sendProfileSubmitted(User $user): void
    {
        $this->sendMember($user->email, 'm02_profile_verification_pending', [
            'memberName' => $this->memberDisplayName($user),
            'heroHeadline' => 'Profile Under Verification',
            'showPortalCta' => true,
        ]);

        $this->sendAdmin('a01_profile_submitted', [
            'memberName' => $this->memberDisplayName($user),
            'email' => $user->email ?? '—',
            'mobile' => $user->mobile ?? '—',
            'submittedOn' => now()->format('d M Y, h:i A'),
        ]);

        $this->sms()->profileSubmitted($user->mobile, $this->memberDisplayName($user));
    }

    public function sendProfileApproved(User $user): void
    {
        $name = $this->memberDisplayName($user);
        $this->sendMember($user->email, 'm03_profile_approved_subscription', [
            'memberName' => $name,
            'heroHeadline' => 'Profile Verified',
            'heroSubtext' => 'You may now complete your membership subscription.',
            'showPortalCta' => true,
        ]);
        $this->sms()->profileVerified($user->mobile, $name);
    }

    public function sendProfileRejected(User $user): void
    {
        $name = $this->memberDisplayName($user);
        $this->sendMember($user->email, 'm04_profile_verification_failed', [
            'memberName' => $name,
            'heroHeadline' => 'Profile Verification Update',
            'showPortalCta' => true,
        ]);
        $this->sms()->profileRejected($user->mobile, $name);
    }

    public function sendMembershipActivated(User $user, MemberSubscription $subscription, PaymentTransaction $transaction): void
    {
        $planLabel = $subscription->subscription_type.' • '.str_replace('_', ' ', (string) $subscription->payment_type);
        $name = $this->memberDisplayName($user);

        $this->sendMember($user->email, 'm05_membership_activated', [
            'memberName' => $name,
            'heroHeadline' => 'Membership Active',
            'heroSubtext' => 'Thank you — your GNAT membership is now active.',
            'showPortalCta' => true,
        ]);

        $txnId = $transaction->razorpay_payment_id ?: ('TXN-'.$transaction->id);

        $this->sendAdmin('a02_subscription_payment', [
            'memberName' => $name,
            'membershipPlan' => $planLabel,
            'transactionId' => $txnId,
            'amount' => 'INR '.number_format((float) $transaction->amount, 2),
            'paymentDate' => ($transaction->paid_at ?? now())->format('d M Y, h:i A'),
        ]);

        $this->sms()->membershipPaymentReceived($user->mobile, $name);
    }

    /**
     * @return array{date: string, time: string}
     */
    public function meetingScheduleParts(Meeting $meeting): array
    {
        $meeting->loadMissing('schedules');
        $schedule = $meeting->schedules->sortBy('meeting_date')->first();
        if (! $schedule?->meeting_date) {
            return ['date' => '—', 'time' => '—'];
        }

        $date = $schedule->meeting_date->format('d M Y');
        $from = $schedule->from_time ? (string) $schedule->from_time : '';
        $to = $schedule->to_time ? (string) $schedule->to_time : '';
        $time = trim($from.($to !== '' ? ' – '.$to : ''));

        return [
            'date' => $date,
            'time' => $time !== '' ? $time : '—',
        ];
    }

    public function sendMeetingInvites(Meeting $meeting, iterable $userIds, ?int $broadcastBatchId = null): void
    {
        $parts = $this->meetingScheduleParts($meeting);
        foreach ($userIds as $userId) {
            $user = User::query()->find($userId);
            if (! $user) {
                continue;
            }

            $invite = MeetingInvite::query()
                ->where('meeting_id', $meeting->id)
                ->where('user_id', $userId)
                ->first();

            if (! $invite) {
                continue;
            }

            $name = $this->memberDisplayName($user);
            $channels = [];

            if ($invite->notify_email) {
                if (! $user->email) {
                    $channels['email'] = ['status' => 'skipped', 'error' => 'No email on file'];
                } else {
                    $channels['email'] = $this->trySendMemberEmail($user->email, 'm10_meeting_schedule', [
                        'memberName' => $name,
                        'meetingDate' => $parts['date'],
                        'meetingTime' => $parts['time'],
                        'heroHeadline' => 'New Meeting Scheduled',
                        'showPortalCta' => true,
                    ]);
                }
            } else {
                $channels['email'] = ['status' => 'skipped', 'error' => 'Not requested'];
            }

            $smsCombined = null;
            if ($invite->notify_sms || $invite->notify_whatsapp) {
                $smsCombined = $this->sms()->trySendScenario('s11_meeting_scheduled', $user->mobile, [$name, $parts['date'], $parts['time']]);
            }

            if ($invite->notify_sms) {
                $channels['sms'] = $smsCombined !== null
                    ? ['status' => $smsCombined['status'], 'error' => $smsCombined['error']]
                    : ['status' => 'skipped', 'error' => 'Not requested'];
            } else {
                $channels['sms'] = ['status' => 'skipped', 'error' => 'Not requested'];
            }

            if ($invite->notify_whatsapp) {
                $channels['whatsapp'] = $smsCombined !== null
                    ? ['status' => $smsCombined['status'], 'error' => $smsCombined['error']]
                    : ['status' => 'skipped', 'error' => 'Not requested'];
            } else {
                $channels['whatsapp'] = ['status' => 'skipped', 'error' => 'Not requested'];
            }

            $this->recordBroadcastChannels($broadcastBatchId, (int) $user->id, $channels);
        }
    }

    /**
     * Reminder for invited members. Each channel sends only if this broadcast enables it and the member’s invite opted in.
     *
     * @param  iterable<int|string>  $userIds
     */
    public function sendMeetingInviteReminders(
        Meeting $meeting,
        iterable $userIds,
        ?int $broadcastBatchId = null,
        bool $broadcastEmail = true,
        bool $broadcastSms = true,
        bool $broadcastWhatsApp = true,
    ): void {
        $parts = $this->meetingScheduleParts($meeting);
        foreach ($userIds as $userId) {
            $user = User::query()->find($userId);
            if (! $user) {
                continue;
            }

            $invite = MeetingInvite::query()
                ->where('meeting_id', $meeting->id)
                ->where('user_id', $userId)
                ->first();

            if (! $invite) {
                continue;
            }

            $name = $this->memberDisplayName($user);
            $channels = [];

            $wantEmail = $broadcastEmail && $invite->notify_email;
            $wantSms = $broadcastSms && $invite->notify_sms;
            $wantWhatsApp = $broadcastWhatsApp && $invite->notify_whatsapp;

            if ($wantEmail) {
                if (! $user->email) {
                    $channels['email'] = ['status' => 'skipped', 'error' => 'No email on file'];
                } else {
                    $channels['email'] = $this->trySendMemberEmail($user->email, 'm29_meeting_invite_reminder', [
                        'memberName' => $name,
                        'meetingDate' => $parts['date'],
                        'meetingTime' => $parts['time'],
                        'heroHeadline' => 'Meeting reminder',
                        'showPortalCta' => true,
                    ]);
                }
            } else {
                $channels['email'] = ['status' => 'skipped', 'error' => 'Not requested'];
            }

            $smsCombined = null;
            if ($wantSms || $wantWhatsApp) {
                $smsCombined = $this->sms()->trySendScenario('s11_meeting_scheduled', $user->mobile, [$name, $parts['date'], $parts['time']]);
            }

            if ($wantSms) {
                $channels['sms'] = $smsCombined !== null
                    ? ['status' => $smsCombined['status'], 'error' => $smsCombined['error']]
                    : ['status' => 'skipped', 'error' => 'Not requested'];
            } else {
                $channels['sms'] = ['status' => 'skipped', 'error' => 'Not requested'];
            }

            if ($wantWhatsApp) {
                $channels['whatsapp'] = $smsCombined !== null
                    ? ['status' => $smsCombined['status'], 'error' => $smsCombined['error']]
                    : ['status' => 'skipped', 'error' => 'Not requested'];
            } else {
                $channels['whatsapp'] = ['status' => 'skipped', 'error' => 'Not requested'];
            }

            $this->recordBroadcastChannels($broadcastBatchId, (int) $user->id, $channels);
        }
    }

    /**
     * Send event invite notifications (email / SMS). WhatsApp uses the same SMS template until a dedicated channel exists.
     */
    public function sendEventInvites(Event $event, iterable $userIds, ?int $broadcastBatchId = null): void
    {
        $event->loadMissing('dates');
        $firstDate = $event->dates->sortBy('event_date')->first();
        $eventDateLabel = $firstDate && $firstDate->event_date
            ? $firstDate->event_date->format('d M Y')
            : '—';

        foreach ($userIds as $userId) {
            $user = User::query()->find($userId);
            if (! $user) {
                continue;
            }

            $invite = EventInvite::query()
                ->where('event_id', $event->id)
                ->where('user_id', $userId)
                ->first();

            if (! $invite) {
                continue;
            }

            $name = $this->memberDisplayName($user);
            $channels = [];

            if ($invite->notify_email) {
                if (! $user->email) {
                    $channels['email'] = ['status' => 'skipped', 'error' => 'No email on file'];
                } else {
                    $channels['email'] = $this->trySendMemberEmail($user->email, 'm13_new_event', [
                        'memberName' => $name,
                        'heroHeadline' => $event->title,
                        'heroSubtext' => 'Event date: '.$eventDateLabel,
                        'showPortalCta' => true,
                    ]);
                }
            } else {
                $channels['email'] = ['status' => 'skipped', 'error' => 'Not requested'];
            }

            $smsCombined = null;
            if ($invite->notify_sms || $invite->notify_whatsapp) {
                $smsCombined = $this->sms()->trySendScenario('s14_new_event', $user->mobile, [$name]);
            }

            if ($invite->notify_sms) {
                $channels['sms'] = $smsCombined !== null
                    ? ['status' => $smsCombined['status'], 'error' => $smsCombined['error']]
                    : ['status' => 'skipped', 'error' => 'Not requested'];
            } else {
                $channels['sms'] = ['status' => 'skipped', 'error' => 'Not requested'];
            }

            if ($invite->notify_whatsapp) {
                $channels['whatsapp'] = $smsCombined !== null
                    ? ['status' => $smsCombined['status'], 'error' => $smsCombined['error']]
                    : ['status' => 'skipped', 'error' => 'Not requested'];
            } else {
                $channels['whatsapp'] = ['status' => 'skipped', 'error' => 'Not requested'];
            }

            $this->recordBroadcastChannels($broadcastBatchId, (int) $user->id, $channels);
        }
    }

    /**
     * Reminder for invited members. Each channel sends only if this broadcast enables it and the member’s invite opted in.
     *
     * @param  iterable<int|string>  $userIds
     */
    public function sendEventInviteReminders(
        Event $event,
        iterable $userIds,
        ?int $broadcastBatchId = null,
        bool $broadcastEmail = true,
        bool $broadcastSms = true,
        bool $broadcastWhatsApp = true,
    ): void {
        $event->loadMissing('dates');
        $firstDate = $event->dates->sortBy('event_date')->first();
        $eventDateLabel = $firstDate && $firstDate->event_date
            ? $firstDate->event_date->format('d M Y')
            : '—';

        foreach ($userIds as $userId) {
            $user = User::query()->find($userId);
            if (! $user) {
                continue;
            }

            $invite = EventInvite::query()
                ->where('event_id', $event->id)
                ->where('user_id', $userId)
                ->first();

            if (! $invite) {
                continue;
            }

            $name = $this->memberDisplayName($user);
            $channels = [];

            $wantEmail = $broadcastEmail && $invite->notify_email;
            $wantSms = $broadcastSms && $invite->notify_sms;
            $wantWhatsApp = $broadcastWhatsApp && $invite->notify_whatsapp;

            if ($wantEmail) {
                if (! $user->email) {
                    $channels['email'] = ['status' => 'skipped', 'error' => 'No email on file'];
                } else {
                    $channels['email'] = $this->trySendMemberEmail($user->email, 'm28_event_invite_reminder', [
                        'memberName' => $name,
                        'heroHeadline' => 'Reminder: '.$event->title,
                        'heroSubtext' => 'Event date: '.$eventDateLabel,
                        'showPortalCta' => true,
                    ]);
                }
            } else {
                $channels['email'] = ['status' => 'skipped', 'error' => 'Not requested'];
            }

            $smsCombined = null;
            if ($wantSms || $wantWhatsApp) {
                $smsCombined = $this->sms()->trySendScenario('s14_new_event', $user->mobile, [$name]);
            }

            if ($wantSms) {
                $channels['sms'] = $smsCombined !== null
                    ? ['status' => $smsCombined['status'], 'error' => $smsCombined['error']]
                    : ['status' => 'skipped', 'error' => 'Not requested'];
            } else {
                $channels['sms'] = ['status' => 'skipped', 'error' => 'Not requested'];
            }

            if ($wantWhatsApp) {
                $channels['whatsapp'] = $smsCombined !== null
                    ? ['status' => $smsCombined['status'], 'error' => $smsCombined['error']]
                    : ['status' => 'skipped', 'error' => 'Not requested'];
            } else {
                $channels['whatsapp'] = ['status' => 'skipped', 'error' => 'Not requested'];
            }

            $this->recordBroadcastChannels($broadcastBatchId, (int) $user->id, $channels);
        }
    }

    public function sendMeetingMemberResponse(User $user, Meeting $meeting, bool $attending): void
    {
        $parts = $this->meetingScheduleParts($meeting);
        $name = $this->memberDisplayName($user);

        if ($attending) {
            $this->sendMember($user->email, 'm11_meeting_attendance_confirmed', [
                'memberName' => $name,
                'meetingDate' => $parts['date'],
                'heroHeadline' => 'Attendance Confirmed',
                'showPortalCta' => true,
            ]);

            $this->sendAdmin('a07_meeting_attendance_confirmed', [
                'memberName' => $name,
                'meetingName' => $meeting->title,
                'meetingDate' => $parts['date'],
            ]);

            $this->sms()->meetingAttendanceConfirmed($user->mobile, $name, $parts['date']);
        } else {
            $this->sendMember($user->email, 'm12_meeting_non_attendance', [
                'memberName' => $name,
                'meetingDate' => $parts['date'],
                'heroHeadline' => 'Response Recorded',
                'showPortalCta' => true,
            ]);

            $this->sendAdmin('a08_meeting_non_attendance', [
                'memberName' => $name,
                'meetingName' => $meeting->title,
                'meetingDate' => $parts['date'],
            ]);

            $this->sms()->meetingNonAttendance($user->mobile, $name, $parts['date']);
        }
    }

    public function sendNominationAlerts(Nomination $nomination, iterable $userIds, ?int $broadcastBatchId = null): void
    {
        foreach ($userIds as $userId) {
            $user = User::query()->find($userId);
            if (! $user) {
                continue;
            }

            $alert = NominationAlert::query()
                ->where('nomination_id', $nomination->id)
                ->where('user_id', $userId)
                ->first();

            if (! $alert) {
                continue;
            }

            $name = $this->memberDisplayName($user);
            $channels = [];

            if ($alert->notify_email) {
                if (! $user->email) {
                    $channels['email'] = ['status' => 'skipped', 'error' => 'No email on file'];
                } else {
                    $channels['email'] = $this->trySendMemberEmail($user->email, 'm16_nomination_live', [
                        'memberName' => $name,
                        'heroHeadline' => 'Nominations Open',
                        'showPortalCta' => true,
                    ]);
                }
            } else {
                $channels['email'] = ['status' => 'skipped', 'error' => 'Not requested'];
            }

            $smsCombined = null;
            if ($alert->notify_sms || $alert->notify_whatsapp) {
                $smsCombined = $this->sms()->trySendScenario('s17_nomination_live', $user->mobile, [$name]);
            }

            if ($alert->notify_sms) {
                $channels['sms'] = $smsCombined !== null
                    ? ['status' => $smsCombined['status'], 'error' => $smsCombined['error']]
                    : ['status' => 'skipped', 'error' => 'Not requested'];
            } else {
                $channels['sms'] = ['status' => 'skipped', 'error' => 'Not requested'];
            }

            if ($alert->notify_whatsapp) {
                $channels['whatsapp'] = $smsCombined !== null
                    ? ['status' => $smsCombined['status'], 'error' => $smsCombined['error']]
                    : ['status' => 'skipped', 'error' => 'Not requested'];
            } else {
                $channels['whatsapp'] = ['status' => 'skipped', 'error' => 'Not requested'];
            }

            $this->recordBroadcastChannels($broadcastBatchId, (int) $user->id, $channels);
        }
    }

    public function sendNominationSubmitted(User $user, NominationPosition $position): void
    {
        $position->loadMissing('nomination');
        $category = $position->position;

        $name = $this->memberDisplayName($user);
        $this->sendMember($user->email, 'm17_nomination_submitted', [
            'memberName' => $name,
            'heroHeadline' => 'Nomination Received',
            'showPortalCta' => true,
        ]);

        $this->sendAdmin('a10_nomination_received', [
            'memberName' => $name,
            'category' => $category,
            'submittedOn' => now()->format('d M Y, h:i A'),
        ]);

        $this->sms()->nominationSubmitted($user->mobile, $name);
    }

    /**
     * GNAT Live Polling Alert (m18) — body copy matches association template; channels respect admin choices.
     */
    public function sendPollingLiveAlert(
        User $user,
        Polling $polling,
        bool $notifyEmail = true,
        bool $notifySms = false,
        bool $notifyWhatsApp = false,
        ?int $broadcastBatchId = null
    ): void {
        $name = $this->memberDisplayName($user);
        $channels = [];

        if ($notifyEmail) {
            if (! $user->email) {
                $channels['email'] = ['status' => 'skipped', 'error' => 'No email on file'];
            } else {
                $channels['email'] = $this->trySendMemberEmail($user->email, 'm18_polling_live', [
                    'memberName' => $name,
                    'heroHeadline' => 'GNAT Live Polling Alert',
                    'heroSubtext' => $polling->title,
                    'showPortalCta' => true,
                ]);
            }
        } else {
            $channels['email'] = ['status' => 'skipped', 'error' => 'Not requested'];
        }

        $smsCombined = null;
        if ($notifySms || $notifyWhatsApp) {
            $smsCombined = $this->sms()->trySendScenario('s19_polling_live', $user->mobile, [$name]);
        }

        if ($notifySms) {
            $channels['sms'] = $smsCombined !== null
                ? ['status' => $smsCombined['status'], 'error' => $smsCombined['error']]
                : ['status' => 'skipped', 'error' => 'Not requested'];
        } else {
            $channels['sms'] = ['status' => 'skipped', 'error' => 'Not requested'];
        }

        if ($notifyWhatsApp) {
            $channels['whatsapp'] = $smsCombined !== null
                ? ['status' => $smsCombined['status'], 'error' => $smsCombined['error']]
                : ['status' => 'skipped', 'error' => 'Not requested'];
        } else {
            $channels['whatsapp'] = ['status' => 'skipped', 'error' => 'Not requested'];
        }

        $this->recordBroadcastChannels($broadcastBatchId, (int) $user->id, $channels);
    }

    /**
     * @param  iterable<int|string>  $userIds
     */
    public function sendPollingLiveAlerts(
        Polling $polling,
        iterable $userIds,
        bool $notifyEmail,
        bool $notifySms,
        bool $notifyWhatsApp,
        ?int $broadcastBatchId = null
    ): void {
        foreach ($userIds as $userId) {
            $user = User::query()->find($userId);
            if (! $user) {
                continue;
            }
            $this->sendPollingLiveAlert($user, $polling, $notifyEmail, $notifySms, $notifyWhatsApp, $broadcastBatchId);
        }
    }

    public function sendPollingVoteRecorded(User $user, Polling $polling): void
    {
        $name = $this->memberDisplayName($user);
        $this->sendMember($user->email, 'm19_polling_response', [
            'memberName' => $name,
            'heroHeadline' => 'Vote Recorded',
            'showPortalCta' => true,
        ]);

        $this->sendAdmin('a11_poll_response', [
            'memberName' => $name,
            'pollTitle' => $polling->title,
            'submittedOn' => now()->format('d M Y, h:i A'),
        ]);

        $this->sms()->pollingResponseRecorded($user->mobile, $name);
    }

    /**
     * GNAT Polling Result Notification (m20) — body matches association template; channels respect admin choices.
     */
    public function sendPollingResultsPublished(
        User $user,
        Polling $polling,
        bool $notifyEmail = true,
        bool $notifySms = false,
        bool $notifyWhatsApp = false,
        ?int $broadcastBatchId = null
    ): void {
        $name = $this->memberDisplayName($user);
        $channels = [];

        if ($notifyEmail) {
            if (! $user->email) {
                $channels['email'] = ['status' => 'skipped', 'error' => 'No email on file'];
            } else {
                $channels['email'] = $this->trySendMemberEmail($user->email, 'm20_polling_results', [
                    'memberName' => $name,
                    'heroHeadline' => 'GNAT Polling Result Notification',
                    'heroSubtext' => $polling->title,
                    'showPortalCta' => true,
                ]);
            }
        } else {
            $channels['email'] = ['status' => 'skipped', 'error' => 'Not requested'];
        }

        $smsCombined = null;
        if ($notifySms || $notifyWhatsApp) {
            $smsCombined = $this->sms()->trySendScenario('s20_polling_results', $user->mobile, [$name]);
        }

        if ($notifySms) {
            $channels['sms'] = $smsCombined !== null
                ? ['status' => $smsCombined['status'], 'error' => $smsCombined['error']]
                : ['status' => 'skipped', 'error' => 'Not requested'];
        } else {
            $channels['sms'] = ['status' => 'skipped', 'error' => 'Not requested'];
        }

        if ($notifyWhatsApp) {
            $channels['whatsapp'] = $smsCombined !== null
                ? ['status' => $smsCombined['status'], 'error' => $smsCombined['error']]
                : ['status' => 'skipped', 'error' => 'Not requested'];
        } else {
            $channels['whatsapp'] = ['status' => 'skipped', 'error' => 'Not requested'];
        }

        $this->recordBroadcastChannels($broadcastBatchId, (int) $user->id, $channels);
    }

    /**
     * @param  iterable<int|string>  $userIds
     */
    public function sendPollingResultsPublishedAlerts(
        Polling $polling,
        iterable $userIds,
        bool $notifyEmail,
        bool $notifySms,
        bool $notifyWhatsApp,
        ?int $broadcastBatchId = null
    ): void {
        foreach ($userIds as $userId) {
            $user = User::query()->find($userId);
            if (! $user) {
                continue;
            }
            $this->sendPollingResultsPublished($user, $polling, $notifyEmail, $notifySms, $notifyWhatsApp, $broadcastBatchId);
        }
    }

    public function sendJobPostingAlerts(AdminJob $job, iterable $userIds, ?int $broadcastBatchId = null): void
    {
        foreach ($userIds as $userId) {
            $user = User::query()->find($userId);
            if (! $user) {
                continue;
            }

            $alert = AdminJobAlert::query()
                ->where('job_id', $job->id)
                ->where('user_id', $userId)
                ->first();

            if (! $alert) {
                continue;
            }

            $name = $this->memberDisplayName($user);
            $channels = [];

            if ($alert->notify_email) {
                if (! $user->email) {
                    $channels['email'] = ['status' => 'skipped', 'error' => 'No email on file'];
                } else {
                    $channels['email'] = $this->trySendMemberEmail($user->email, 'm21_job_posting', [
                        'memberName' => $name,
                        'heroHeadline' => 'New Job Posting Alert',
                        'heroSubtext' => trim($job->title.($job->code ? ' • Code '.$job->code : '')),
                        'showPortalCta' => true,
                    ]);
                }
            } else {
                $channels['email'] = ['status' => 'skipped', 'error' => 'Not requested'];
            }

            $smsCombined = null;
            if ($alert->notify_sms || $alert->notify_whatsapp) {
                $smsCombined = $this->sms()->trySendScenario('s22_job_posting', $user->mobile, [$name]);
            }

            if ($alert->notify_sms) {
                $channels['sms'] = $smsCombined !== null
                    ? ['status' => $smsCombined['status'], 'error' => $smsCombined['error']]
                    : ['status' => 'skipped', 'error' => 'Not requested'];
            } else {
                $channels['sms'] = ['status' => 'skipped', 'error' => 'Not requested'];
            }

            if ($alert->notify_whatsapp) {
                $channels['whatsapp'] = $smsCombined !== null
                    ? ['status' => $smsCombined['status'], 'error' => $smsCombined['error']]
                    : ['status' => 'skipped', 'error' => 'Not requested'];
            } else {
                $channels['whatsapp'] = ['status' => 'skipped', 'error' => 'Not requested'];
            }

            $this->recordBroadcastChannels($broadcastBatchId, (int) $user->id, $channels);
        }
    }

    public function sendJobApplicationSubmitted(User $user, AdminJob $job): void
    {
        $name = $this->memberDisplayName($user);
        $this->sendMember($user->email, 'm22_job_application_confirmation', [
            'memberName' => $name,
            'jobCode' => $job->code,
            'heroHeadline' => 'GNAT Job Application Confirmation',
            'heroSubtext' => 'Job code '.$job->code,
            'showPortalCta' => true,
        ]);

        $this->sendAdmin('a12_job_application', [
            'memberName' => $name,
            'jobTitle' => $job->title,
            'companyName' => $job->hospital ?? 'GNAT Association',
            'applicationDate' => now()->format('d M Y, h:i A'),
        ]);

        $this->sms()->jobApplicationSubmitted($user->mobile, $name);
    }

    public function sendJobApplicationStatusToMember(User $user, AdminJobApplication $application): void
    {
        $name = $this->memberDisplayName($user);
        $status = (string) $application->application_status;

        if ($status === 'selected') {
            $this->sendMember($user->email, 'm25_job_application_selected', [
                'memberName' => $name,
                'heroHeadline' => 'GNAT Job Communication Update',
                'heroSubtext' => 'Your application status is now selected.',
                'showPortalCta' => true,
            ]);
            $this->sms()->jobApplicationCommunication($user->mobile, $name);

            return;
        }

        if (in_array($status, ['not_selected', 'joined', 'not_joined'], true)) {
            $this->sms()->jobApplicationPortalStatusUpdated($user->mobile, $name);
        }
    }

    public function sendNeedJobRequestStatus(MemberJobRequest $row): void
    {
        $email = $row->email;
        $name = $row->name ?: 'Member';

        if ($row->status === 'reviewed') {
            $this->sendMember($email, 'm23_job_request_reviewed', [
                'memberName' => $name,
                'heroHeadline' => 'GNAT Job Request Status Update',
                'heroSubtext' => 'Your request has been reviewed.',
                'showPortalCta' => true,
            ]);
            $this->sms()->needJobRequestReviewed($row->mobile, $name);
        } elseif ($row->status === 'contacted') {
            $this->sendMember($email, 'm24_job_request_contact', [
                'memberName' => $name,
                'heroHeadline' => 'GNAT Job Request Status Update',
                'heroSubtext' => 'Communication has been initiated on your request.',
                'showPortalCta' => true,
            ]);
            $this->sms()->jobApplicationCommunication($row->mobile, $name);
        }
    }

    public function sendNewEventBroadcast(Event $event): void
    {
        if ($event->member_notification_sent_at) {
            return;
        }

        $event->loadMissing('dates');
        $firstDate = $event->dates->sortBy('event_date')->first();
        $eventDateLabel = $firstDate && $firstDate->event_date
            ? $firstDate->event_date->format('d M Y')
            : '—';

        User::query()
            ->where('is_approved', true)
            ->whereHas('activeSubscription')
            ->where(function ($q) {
                $q->whereNotNull('email')
                    ->orWhereNotNull('mobile');
            })
            ->orderBy('id')
            ->chunkById(80, function ($users) use ($event, $eventDateLabel) {
                foreach ($users as $user) {
                    $display = $this->memberDisplayName($user);
                    $this->sendMember($user->email, 'm13_new_event', [
                        'memberName' => $display,
                        'heroHeadline' => $event->title,
                        'heroSubtext' => 'Event date: '.$eventDateLabel,
                        'showPortalCta' => true,
                    ]);
                    $this->sms()->newEventUpdate($user->mobile, $display);
                }
            });

        $event->forceFill(['member_notification_sent_at' => now()])->save();
    }

    public function sendEventInterestConfirmation(string $email, string $memberName, Event $event, ?string $phone = null): void
    {
        $event->loadMissing('dates');
        $firstDate = $event->dates->sortBy('event_date')->first();
        $eventDateLabel = $firstDate && $firstDate->event_date
            ? $firstDate->event_date->format('d M Y')
            : '—';

        $this->sendMember($email, 'm14_event_interest', [
            'memberName' => $memberName,
            'heroHeadline' => 'Interest Recorded',
            'showPortalCta' => true,
        ]);

        $this->sendAdmin('a09_event_interest', [
            'memberName' => $memberName,
            'eventName' => $event->title,
            'eventDate' => $eventDateLabel,
        ]);

        $this->sms()->eventInterestRecorded($phone, $memberName);
    }

    public function sendEventParticipationConfirmation(User $user, Event $event): void
    {
        $name = $this->memberDisplayName($user);
        $this->sendMember($user->email, 'm15_event_participation', [
            'memberName' => $name,
            'heroHeadline' => 'Participation Recorded',
            'showPortalCta' => true,
        ]);
        $this->sms()->eventParticipationRecorded($user->mobile, $name);
    }

    /**
     * Template 15 for guests / any attendee reached by email (public registration, QR scan, etc.).
     */
    public function sendEventParticipationConfirmationByEmail(string $email, string $attendeeName, Event $event, ?string $phone = null): void
    {
        $email = trim($email);
        if ($email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->sendMember($email, 'm15_event_participation', [
                'memberName' => $attendeeName !== '' ? $attendeeName : 'Member',
                'heroHeadline' => 'Participation Recorded',
                'showPortalCta' => true,
            ]);
        }

        $this->sms()->eventParticipationRecorded(
            $phone,
            $attendeeName !== '' ? $attendeeName : 'Member'
        );
    }

    public function sendDonationReceipt(DonationPayment $payment): void
    {
        $name = $payment->donor_name ?: 'Supporter';

        $this->sendMember($payment->donor_email, 'm26_donation_confirmation', [
            'memberName' => $name,
            'heroHeadline' => 'Thank You',
            'showPortalCta' => false,
        ]);

        $txnId = $payment->payment_id ?: $payment->order_id ?: ('DN-'.$payment->id);

        $this->sendAdmin('a13_donation_received', [
            'donorName' => $name,
            'transactionId' => $txnId,
            'amount' => 'INR '.number_format((float) $payment->amount, 2),
            'paymentDate' => now()->format('d M Y, h:i A'),
        ]);

        $this->sms()->donationReceived($payment->donor_mobile, $name);
    }

    public function sendSupportConfirmation(string $email, string $memberName, ?string $phone = null): void
    {
        $this->sendMember($email, 'm27_support_confirmation', [
            'memberName' => $memberName,
            'heroHeadline' => 'Support Request Received',
            'showPortalCta' => false,
        ]);
        $this->sms()->supportRequestReceived($phone, $memberName);
    }

    public function sendWebsiteContactAdmin(array $payload): void
    {
        $ticketId = strtoupper(substr(sha1(($payload['email'] ?? '').now()->timestamp), 0, 10));

        $visitorEmail = trim((string) ($payload['email'] ?? ''));
        $visitorName = trim((string) ($payload['name'] ?? 'Visitor'));
        $replyTo = $visitorEmail !== ''
            ? [new Address($visitorEmail, $visitorName !== '' ? $visitorName : null)]
            : [];

        $this->sendAdmin('a14_support_request', [
            'memberName' => $payload['name'] ?? 'Visitor',
            'ticketId' => $ticketId,
            'supportSubject' => $payload['subject'] ?? 'General',
            'submittedOn' => now()->format('d M Y, h:i A'),
            'supportBody' => $payload['message'] ?? '',
        ], $replyTo);
    }

    /**
     * Renewal notice to member + admin copy (templates 6 & admin 3).
     */
    public function sendRenewalReminder(User $user, MemberSubscription $subscription): void
    {
        $expiry = $subscription->end_date?->format('d M Y') ?? '—';
        $name = $this->memberDisplayName($user);

        $this->sendMember($user->email, 'm06_renewal_reminder', [
            'memberName' => $name,
            'expiryDate' => $expiry,
            'heroHeadline' => 'Renewal Reminder',
            'showPortalCta' => true,
        ]);

        $this->sendAdmin('a03_renewal_reminder_sent', [
            'memberName' => $name,
            'expiryDate' => $expiry,
        ]);

        $this->sms()->membershipExpiryReminder($user->mobile, $name, $expiry);
    }

    /**
     * Templates 7 & admin 4.
     */
    public function sendMembershipExpiredNotice(User $user, MemberSubscription $subscription): void
    {
        $expiry = $subscription->end_date?->format('d M Y') ?? '—';
        $name = $this->memberDisplayName($user);

        $this->sendMember($user->email, 'm07_membership_expired', [
            'memberName' => $name,
            'expiryDate' => $expiry,
            'heroHeadline' => 'Membership Expired',
            'showPortalCta' => true,
        ]);

        $this->sendAdmin('a04_membership_expired', [
            'memberName' => $name,
            'membershipId' => (string) $subscription->id,
            'expiryDate' => $expiry,
        ]);

        $this->sms()->membershipExpired($user->mobile, $name, $expiry);
    }

    /**
     * Templates 9 & admin 5.
     */
    public function sendInactiveAccountNotice(User $user, ?MemberSubscription $lastSubscription): void
    {
        $pendingSince = $lastSubscription?->end_date?->format('d M Y') ?? '—';
        $name = $this->memberDisplayName($user);

        $this->sendMember($user->email, 'm09_account_inactive_pending_subscription', [
            'memberName' => $name,
            'heroHeadline' => 'Account Inactive',
            'showPortalCta' => true,
        ]);

        $this->sendAdmin('a05_account_inactive', [
            'memberName' => $name,
            'membershipId' => $lastSubscription ? (string) $lastSubscription->id : '—',
            'pendingSince' => $pendingSince,
        ]);

        $this->sms()->accountInactivePendingSubscription($user->mobile, $name);
    }

    /**
     * Renewal reminder, natural expiry (m07), and inactive-account notices for one member.
     * Called from member-portal middleware on each request (no scheduler).
     */
    public function runMembershipLifecycleForUser(User $user): void
    {
        $user->refresh();

        $hasEmail = $user->email !== null && trim((string) $user->email) !== '';
        $hasMobile = $this->sms()->normalizeMobile($user->mobile) !== null;
        if (! $hasEmail && ! $hasMobile) {
            return;
        }

        $daysBefore = max(1, (int) config('gnat_mail.renewal_reminder_days_before', 14));
        $inactiveDays = max(1, (int) config('gnat_mail.inactive_after_subscription_days', 90));
        $reminderTargetDate = Carbon::today()->addDays($daysBefore)->toDateString();
        $today = Carbon::today()->toDateString();
        $inactiveCutoff = Carbon::today()->subDays($inactiveDays)->toDateString();

        $activeSubs = MemberSubscription::query()
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->orderBy('id')
            ->get();

        foreach ($activeSubs as $sub) {
            $end = $sub->end_date?->toDateString();
            if ($end === null) {
                continue;
            }

            if ($end < $today && $sub->expiry_notification_sent_at === null) {
                $this->sendMembershipExpiredNotice($user->fresh(), $sub);
                $sub->forceFill([
                    'status' => 'expired',
                    'expiry_notification_sent_at' => now(),
                ])->save();

                continue;
            }

            if ($end === $reminderTargetDate && $sub->renewal_reminder_sent_at === null) {
                $this->sendRenewalReminder($user->fresh(), $sub);
                $sub->forceFill(['renewal_reminder_sent_at' => now()])->save();
            }
        }

        $user->refresh();

        if (! $user->is_approved || ! $user->profile_completed) {
            return;
        }

        if ($user->activeSubscription()->exists()) {
            return;
        }

        if ($user->gnat_inactive_notice_sent_at !== null) {
            return;
        }

        $lastEligible = MemberSubscription::query()
            ->where('user_id', $user->id)
            ->where('status', 'expired')
            ->whereDate('end_date', '<=', $inactiveCutoff)
            ->orderByDesc('end_date')
            ->first();

        if (! $lastEligible) {
            return;
        }

        $this->sendInactiveAccountNotice($user->fresh(), $lastEligible);
        $user->forceFill(['gnat_inactive_notice_sent_at' => now()])->save();
    }

    /**
     * Member template 8 and admin template 6 — call from your cancellation workflow when implemented.
     */
    public function sendMembershipCancellationConfirmed(User $user, MemberSubscription $subscription): void
    {
        $name = $this->memberDisplayName($user);
        $this->sendMember($user->email, 'm08_membership_cancellation', [
            'memberName' => $name,
            'heroHeadline' => 'Cancellation Confirmed',
            'showPortalCta' => true,
        ]);

        $this->sendAdmin('a06_cancellation', [
            'memberName' => $name,
            'membershipId' => (string) $subscription->id,
            'cancellationDate' => now()->format('d M Y, h:i A'),
        ]);

        $this->sms()->membershipCancellation($user->mobile, $name);
    }

    /**
     * @param  array<string, mixed>  $viewData
     * @return array{status: string, error: ?string}
     */
    private function trySendMemberEmail(string $email, string $templateKey, array $viewData = []): array
    {
        $email = trim($email);
        if ($email === '') {
            return ['status' => 'skipped', 'error' => 'No email on file'];
        }

        $subject = self::MEMBER_SUBJECTS[$templateKey] ?? 'GNAT Association';
        $viewData['portalUrl'] = $viewData['portalUrl'] ?? $this->memberPortalUrl();

        try {
            Mail::to($email)->send(new GnatMemberNotification($templateKey, $subject, $viewData));

            return ['status' => 'success', 'error' => null];
        } catch (\Throwable $e) {
            Log::warning('GNAT mail send failed', [
                'message' => $e->getMessage(),
            ]);

            return ['status' => 'failed', 'error' => $e->getMessage()];
        }
    }

    /**
     * @param  array<string, array{status: string, error?: ?string}>  $channels
     */
    private function recordBroadcastChannels(?int $broadcastBatchId, int $userId, array $channels): void
    {
        if ($broadcastBatchId === null) {
            return;
        }

        GnatNotificationDeliveryLog::recordChannelResults($broadcastBatchId, $userId, $channels);
    }

    private function safeSend(callable $callback): void
    {
        try {
            $callback();
        } catch (\Throwable $e) {
            Log::warning('GNAT mail send failed', [
                'message' => $e->getMessage(),
            ]);
        }
    }
}
