<?php

namespace App\Services;

use App\Mail\GnatAdminNotification;
use App\Mail\GnatMemberNotification;
use App\Models\AdminJob;
use App\Models\AdminJobApplication;
use App\Models\DonationPayment;
use App\Models\Event;
use App\Models\AdminJobAlert;
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
        $this->sendMember($user->email, 'm01_registration_successful', [
            'memberName' => $this->memberDisplayName($user),
            'heroHeadline' => 'Welcome to GNAT',
            'heroSubtext' => 'Your registration is complete. Complete your profile to unlock member services.',
            'showPortalCta' => true,
        ]);
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
    }

    public function sendProfileApproved(User $user): void
    {
        $this->sendMember($user->email, 'm03_profile_approved_subscription', [
            'memberName' => $this->memberDisplayName($user),
            'heroHeadline' => 'Profile Verified',
            'heroSubtext' => 'You may now complete your membership subscription.',
            'showPortalCta' => true,
        ]);
    }

    public function sendProfileRejected(User $user): void
    {
        $this->sendMember($user->email, 'm04_profile_verification_failed', [
            'memberName' => $this->memberDisplayName($user),
            'heroHeadline' => 'Profile Verification Update',
            'showPortalCta' => true,
        ]);
    }

    public function sendMembershipActivated(User $user, MemberSubscription $subscription, PaymentTransaction $transaction): void
    {
        $planLabel = $subscription->subscription_type.' • '.str_replace('_', ' ', (string) $subscription->payment_type);

        $this->sendMember($user->email, 'm05_membership_activated', [
            'memberName' => $this->memberDisplayName($user),
            'heroHeadline' => 'Membership Active',
            'heroSubtext' => 'Thank you — your GNAT membership is now active.',
            'showPortalCta' => true,
        ]);

        $txnId = $transaction->razorpay_payment_id ?: ('TXN-'.$transaction->id);

        $this->sendAdmin('a02_subscription_payment', [
            'memberName' => $this->memberDisplayName($user),
            'membershipPlan' => $planLabel,
            'transactionId' => $txnId,
            'amount' => 'INR '.number_format((float) $transaction->amount, 2),
            'paymentDate' => ($transaction->paid_at ?? now())->format('d M Y, h:i A'),
        ]);
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

    public function sendMeetingInvites(Meeting $meeting, iterable $userIds): void
    {
        $parts = $this->meetingScheduleParts($meeting);
        foreach ($userIds as $userId) {
            $user = User::query()->find($userId);
            if (! $user || ! $user->email) {
                continue;
            }

            $invite = MeetingInvite::query()
                ->where('meeting_id', $meeting->id)
                ->where('user_id', $userId)
                ->first();

            if (! $invite || ! $invite->notify_email) {
                continue;
            }

            $this->sendMember($user->email, 'm10_meeting_schedule', [
                'memberName' => $this->memberDisplayName($user),
                'meetingDate' => $parts['date'],
                'meetingTime' => $parts['time'],
                'heroHeadline' => 'New Meeting Scheduled',
                'showPortalCta' => true,
            ]);
        }
    }

    public function sendMeetingMemberResponse(User $user, Meeting $meeting, bool $attending): void
    {
        $parts = $this->meetingScheduleParts($meeting);

        if ($attending) {
            $this->sendMember($user->email, 'm11_meeting_attendance_confirmed', [
                'memberName' => $this->memberDisplayName($user),
                'meetingDate' => $parts['date'],
                'heroHeadline' => 'Attendance Confirmed',
                'showPortalCta' => true,
            ]);

            $this->sendAdmin('a07_meeting_attendance_confirmed', [
                'memberName' => $this->memberDisplayName($user),
                'meetingName' => $meeting->title,
                'meetingDate' => $parts['date'],
            ]);
        } else {
            $this->sendMember($user->email, 'm12_meeting_non_attendance', [
                'memberName' => $this->memberDisplayName($user),
                'meetingDate' => $parts['date'],
                'heroHeadline' => 'Response Recorded',
                'showPortalCta' => true,
            ]);

            $this->sendAdmin('a08_meeting_non_attendance', [
                'memberName' => $this->memberDisplayName($user),
                'meetingName' => $meeting->title,
                'meetingDate' => $parts['date'],
            ]);
        }
    }

    public function sendNominationAlerts(Nomination $nomination, iterable $userIds): void
    {
        foreach ($userIds as $userId) {
            $user = User::query()->find($userId);
            if (! $user || ! $user->email) {
                continue;
            }

            $alert = NominationAlert::query()
                ->where('nomination_id', $nomination->id)
                ->where('user_id', $userId)
                ->first();

            if (! $alert || ! $alert->notify_email) {
                continue;
            }

            $this->sendMember($user->email, 'm16_nomination_live', [
                'memberName' => $this->memberDisplayName($user),
                'heroHeadline' => 'Nominations Open',
                'showPortalCta' => true,
            ]);
        }
    }

    public function sendNominationSubmitted(User $user, NominationPosition $position): void
    {
        $position->loadMissing('nomination');
        $category = $position->position;

        $this->sendMember($user->email, 'm17_nomination_submitted', [
            'memberName' => $this->memberDisplayName($user),
            'heroHeadline' => 'Nomination Received',
            'showPortalCta' => true,
        ]);

        $this->sendAdmin('a10_nomination_received', [
            'memberName' => $this->memberDisplayName($user),
            'category' => $category,
            'submittedOn' => now()->format('d M Y, h:i A'),
        ]);
    }

    public function sendPollingLiveAlert(User $user, Polling $polling): void
    {
        $this->sendMember($user->email, 'm18_polling_live', [
            'memberName' => $this->memberDisplayName($user),
            'heroHeadline' => 'Polling Is Live',
            'showPortalCta' => true,
        ]);
    }

    public function sendPollingVoteRecorded(User $user, Polling $polling): void
    {
        $this->sendMember($user->email, 'm19_polling_response', [
            'memberName' => $this->memberDisplayName($user),
            'heroHeadline' => 'Vote Recorded',
            'showPortalCta' => true,
        ]);

        $this->sendAdmin('a11_poll_response', [
            'memberName' => $this->memberDisplayName($user),
            'pollTitle' => $polling->title,
            'submittedOn' => now()->format('d M Y, h:i A'),
        ]);
    }

    public function sendPollingResultsPublished(User $user, Polling $polling): void
    {
        $this->sendMember($user->email, 'm20_polling_results', [
            'memberName' => $this->memberDisplayName($user),
            'heroHeadline' => 'Results Published',
            'showPortalCta' => true,
        ]);
    }

    public function sendJobPostingAlerts(AdminJob $job, iterable $userIds): void
    {
        foreach ($userIds as $userId) {
            $user = User::query()->find($userId);
            if (! $user || ! $user->email) {
                continue;
            }

            $alert = AdminJobAlert::query()
                ->where('job_id', $job->id)
                ->where('user_id', $userId)
                ->first();

            if (! $alert || ! $alert->notify_email) {
                continue;
            }

            $this->sendMember($user->email, 'm21_job_posting', [
                'memberName' => $this->memberDisplayName($user),
                'heroHeadline' => 'New Job Posting',
                'showPortalCta' => true,
            ]);
        }
    }

    public function sendJobApplicationSubmitted(User $user, AdminJob $job): void
    {
        $this->sendMember($user->email, 'm22_job_application_confirmation', [
            'memberName' => $this->memberDisplayName($user),
            'jobCode' => $job->code,
            'heroHeadline' => 'Application Received',
            'showPortalCta' => true,
        ]);

        $this->sendAdmin('a12_job_application', [
            'memberName' => $this->memberDisplayName($user),
            'jobTitle' => $job->title,
            'companyName' => $job->hospital ?? 'GNAT Association',
            'applicationDate' => now()->format('d M Y, h:i A'),
        ]);
    }

    public function sendJobApplicationStatusToMember(User $user, AdminJobApplication $application): void
    {
        if ($application->application_status === 'selected') {
            $this->sendMember($user->email, 'm25_job_application_selected', [
                'memberName' => $this->memberDisplayName($user),
                'heroHeadline' => 'Congratulations',
                'showPortalCta' => true,
            ]);
        }
    }

    public function sendNeedJobRequestStatus(MemberJobRequest $row): void
    {
        $email = $row->email;
        $name = $row->name ?: 'Member';

        if ($row->status === 'reviewed') {
            $this->sendMember($email, 'm23_job_request_reviewed', [
                'memberName' => $name,
                'heroHeadline' => 'Job Request Update',
                'showPortalCta' => true,
            ]);
        } elseif ($row->status === 'contacted') {
            $this->sendMember($email, 'm24_job_request_contact', [
                'memberName' => $name,
                'heroHeadline' => 'Job Request Update',
                'showPortalCta' => true,
            ]);
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
            ->whereNotNull('email')
            ->orderBy('id')
            ->chunkById(80, function ($users) use ($event, $eventDateLabel) {
                foreach ($users as $user) {
                    $this->sendMember($user->email, 'm13_new_event', [
                        'memberName' => $this->memberDisplayName($user),
                        'heroHeadline' => $event->title,
                        'heroSubtext' => 'Event date: '.$eventDateLabel,
                        'showPortalCta' => true,
                    ]);
                }
            });

        $event->forceFill(['member_notification_sent_at' => now()])->save();
    }

    public function sendEventInterestConfirmation(string $email, string $memberName, Event $event): void
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
    }

    public function sendEventParticipationConfirmation(User $user, Event $event): void
    {
        $this->sendMember($user->email, 'm15_event_participation', [
            'memberName' => $this->memberDisplayName($user),
            'heroHeadline' => 'Participation Recorded',
            'showPortalCta' => true,
        ]);
    }

    /**
     * Template 15 for guests / any attendee reached by email (public registration, QR scan, etc.).
     */
    public function sendEventParticipationConfirmationByEmail(string $email, string $attendeeName, Event $event): void
    {
        $email = trim($email);
        if ($email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return;
        }

        $this->sendMember($email, 'm15_event_participation', [
            'memberName' => $attendeeName !== '' ? $attendeeName : 'Member',
            'heroHeadline' => 'Participation Recorded',
            'showPortalCta' => true,
        ]);
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
    }

    public function sendSupportConfirmation(string $email, string $memberName): void
    {
        $this->sendMember($email, 'm27_support_confirmation', [
            'memberName' => $memberName,
            'heroHeadline' => 'Support Request Received',
            'showPortalCta' => false,
        ]);
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

        $this->sendMember($user->email, 'm06_renewal_reminder', [
            'memberName' => $this->memberDisplayName($user),
            'expiryDate' => $expiry,
            'heroHeadline' => 'Renewal Reminder',
            'showPortalCta' => true,
        ]);

        $this->sendAdmin('a03_renewal_reminder_sent', [
            'memberName' => $this->memberDisplayName($user),
            'expiryDate' => $expiry,
        ]);
    }

    /**
     * Templates 7 & admin 4.
     */
    public function sendMembershipExpiredNotice(User $user, MemberSubscription $subscription): void
    {
        $expiry = $subscription->end_date?->format('d M Y') ?? '—';

        $this->sendMember($user->email, 'm07_membership_expired', [
            'memberName' => $this->memberDisplayName($user),
            'expiryDate' => $expiry,
            'heroHeadline' => 'Membership Expired',
            'showPortalCta' => true,
        ]);

        $this->sendAdmin('a04_membership_expired', [
            'memberName' => $this->memberDisplayName($user),
            'membershipId' => (string) $subscription->id,
            'expiryDate' => $expiry,
        ]);
    }

    /**
     * Templates 9 & admin 5.
     */
    public function sendInactiveAccountNotice(User $user, ?MemberSubscription $lastSubscription): void
    {
        $pendingSince = $lastSubscription?->end_date?->format('d M Y') ?? '—';

        $this->sendMember($user->email, 'm09_account_inactive_pending_subscription', [
            'memberName' => $this->memberDisplayName($user),
            'heroHeadline' => 'Account Inactive',
            'showPortalCta' => true,
        ]);

        $this->sendAdmin('a05_account_inactive', [
            'memberName' => $this->memberDisplayName($user),
            'membershipId' => $lastSubscription ? (string) $lastSubscription->id : '—',
            'pendingSince' => $pendingSince,
        ]);
    }

    /**
     * Renewal reminder, natural expiry (m07), and inactive-account notices for one member.
     * Called from member-portal middleware on each request (no scheduler).
     */
    public function runMembershipLifecycleForUser(User $user): void
    {
        $user->refresh();

        if ($user->email === null || trim((string) $user->email) === '') {
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
        $this->sendMember($user->email, 'm08_membership_cancellation', [
            'memberName' => $this->memberDisplayName($user),
            'heroHeadline' => 'Cancellation Confirmed',
            'showPortalCta' => true,
        ]);

        $this->sendAdmin('a06_cancellation', [
            'memberName' => $this->memberDisplayName($user),
            'membershipId' => (string) $subscription->id,
            'cancellationDate' => now()->format('d M Y, h:i A'),
        ]);
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
