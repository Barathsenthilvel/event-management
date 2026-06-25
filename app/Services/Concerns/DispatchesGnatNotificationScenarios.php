<?php

namespace App\Services\Concerns;

/**
 * Shared scenario helpers — same keys as gnat_sms / gnat_whatsapp config.
 *
 * @mixin \App\Services\GnatSmsService|\App\Services\GnatWhatsAppService
 */
trait DispatchesGnatNotificationScenarios
{
    public function registrationComplete(?string $mobile, string $memberName): void
    {
        $this->sendScenario('s01_registration_complete', $mobile, [$memberName]);
    }

    public function profileSubmitted(?string $mobile, string $memberName): void
    {
        $this->sendScenario('s02_profile_submitted', $mobile, [$memberName]);
    }

    public function profileVerified(?string $mobile, string $memberName): void
    {
        $this->sendScenario('s03_profile_verified', $mobile, [$memberName]);
    }

    public function profileRejected(?string $mobile, string $memberName): void
    {
        $this->sendScenario('s04_profile_rejected', $mobile, [$memberName]);
    }

    public function membershipPaymentReceived(?string $mobile, string $memberName): void
    {
        $this->sendScenario('s05_membership_payment_received', $mobile, [$memberName]);
    }

    public function membershipExpiryReminder(?string $mobile, string $memberName, string $expiryDate): void
    {
        $this->sendScenario('s06_membership_expiry_reminder', $mobile, [$memberName, $expiryDate]);
    }

    public function membershipExpired(?string $mobile, string $memberName, string $expiryDate): void
    {
        $this->sendScenario('s07_membership_expired', $mobile, [$memberName, $expiryDate]);
    }

    public function accountInactivePendingSubscription(?string $mobile, string $memberName): void
    {
        $this->sendScenario('s08_account_inactive_90_days', $mobile, [$memberName]);
    }

    public function membershipCancellation(?string $mobile, string $memberName): void
    {
        $this->sendScenario('s09_membership_cancellation', $mobile, [$memberName]);
    }

    public function meetingScheduled(?string $mobile, string $memberName, string $date, string $time): void
    {
        $this->sendScenario('s11_meeting_scheduled', $mobile, [$memberName, $date, $time]);
    }

    public function meetingReminder(?string $mobile, string $memberName, string $date, string $time): void
    {
        $this->sendScenario('s29_meeting_reminder', $mobile, [$memberName, $date, $time]);
    }

    public function meetingCancelled(?string $mobile, string $memberName, string $meetingDate): void
    {
        $this->sendScenario('s30_meeting_cancelled', $mobile, [$memberName, $meetingDate]);
    }

    public function meetingAttendanceConfirmed(?string $mobile, string $memberName, string $meetingDate): void
    {
        $this->sendScenario('s12_meeting_attendance_confirmed', $mobile, [$memberName, $meetingDate]);
    }

    public function meetingNonAttendance(?string $mobile, string $memberName, string $meetingDate): void
    {
        $this->sendScenario('s13_meeting_non_attendance', $mobile, [$memberName, $meetingDate]);
    }

    public function newEventUpdate(?string $mobile, string $memberName): void
    {
        $this->sendScenario('s14_new_event', $mobile, [$memberName]);
    }

    public function eventReminder(?string $mobile, string $memberName): void
    {
        $this->sendScenario('s31_event_reminder', $mobile, [$memberName]);
    }

    public function eventCancelled(?string $mobile, string $memberName): void
    {
        $this->sendScenario('s34_event_cancelled', $mobile, [$memberName]);
    }

    public function eventInterestRecorded(?string $mobile, string $memberName): void
    {
        $this->sendScenario('s15_event_interest', $mobile, [$memberName]);
    }

    public function eventParticipationRecorded(?string $mobile, string $memberName): void
    {
        $this->sendScenario('s16_event_participation', $mobile, [$memberName]);
    }

    public function nominationLive(?string $mobile, string $memberName): void
    {
        $this->sendScenario('s17_nomination_live', $mobile, [$memberName]);
    }

    public function nominationReminder(?string $mobile, string $memberName): void
    {
        $this->sendScenario('s32_nomination_reminder', $mobile, [$memberName]);
    }

    public function nominationSubmitted(?string $mobile, string $memberName): void
    {
        $this->sendScenario('s18_nomination_submitted', $mobile, [$memberName]);
    }

    public function pollingLive(?string $mobile, string $memberName): void
    {
        $this->sendScenario('s19_polling_live', $mobile, [$memberName]);
    }

    public function pollingReminder(?string $mobile, string $memberName): void
    {
        $this->sendScenario('s33_polling_reminder', $mobile, [$memberName]);
    }

    public function pollingResults(?string $mobile, string $memberName): void
    {
        $this->sendScenario('s20_polling_results', $mobile, [$memberName]);
    }

    public function pollingResponseRecorded(?string $mobile, string $memberName): void
    {
        $this->sendScenario('s21_polling_response', $mobile, [$memberName]);
    }

    public function jobPostingAlert(?string $mobile, string $memberName): void
    {
        $this->sendScenario('s22_job_posting', $mobile, [$memberName]);
    }

    public function jobApplicationSubmitted(?string $mobile, string $memberName): void
    {
        $this->sendScenario('s23_job_application_submitted', $mobile, [$memberName]);
    }

    public function needJobRequestReviewed(?string $mobile, string $memberName): void
    {
        $this->sendScenario('s24_job_request_reviewed', $mobile, [$memberName]);
    }

    public function needJobRequestStatusUpdated(?string $mobile, string $memberName): void
    {
        $this->sendScenario('s25_job_request_status_updated', $mobile, [$memberName]);
    }

    public function jobApplicationPortalStatusUpdated(?string $mobile, string $memberName): void
    {
        $this->sendScenario('s25_job_request_status_updated', $mobile, [$memberName]);
    }

    public function jobApplicationCommunication(?string $mobile, string $memberName): void
    {
        $this->sendScenario('s26_job_request_communication', $mobile, [$memberName]);
    }

    public function donationReceived(?string $mobile, string $donorName): void
    {
        $this->sendScenario('s27_donation_received', $mobile, [$donorName]);
    }

    public function supportRequestReceived(?string $mobile, string $name): void
    {
        $this->sendScenario('s28_support_request', $mobile, [$name]);
    }
}
