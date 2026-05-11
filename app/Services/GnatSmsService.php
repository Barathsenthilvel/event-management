<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * GNAT transactional SMS — templates align with association copy; MSG91 Flow API.
 */
class GnatSmsService
{
    /** @var array<string, string> */
    private const TEMPLATES = [
        's01_registration_complete' => 'Dear {#var#}, your GNAT registration is completed successfully. Kindly complete pending profile details to proceed further. Team GNAT',
        's02_profile_submitted' => 'Dear {#var#}, your profile submission has been received successfully and is under verification. Updates will be shared after review completion. Team GNAT',
        's03_profile_verified' => 'Dear {#var#}, your profile verification is completed successfully. Kindly complete membership subscription to activate account access. Team GNAT',
        's04_profile_rejected' => 'Dear {#var#}, your profile verification could not be completed due to incomplete or invalid details. Kindly contact GNAT support. Team GNAT',
        's05_membership_payment_received' => 'Dear {#var#}, your membership payment has been received successfully and your account is now active. Team GNAT',
        's06_membership_expiry_reminder' => 'Dear {#var#}, your GNAT membership validity is nearing expiry on {#var#}. Kindly renew before the due date to continue account access. Team GNAT',
        's07_membership_expired' => 'Dear {#var#}, your GNAT membership validity has expired on {#var#}. Kindly renew your membership to continue account access. Team GNAT',
        's08_account_inactive_90_days' => 'Dear {#var#}, your GNAT account is inactive due to pending membership subscription for the last 90 days. Kindly contact GNAT support for assistance. Team GNAT',
        's09_membership_cancellation' => 'Dear {#var#}, your GNAT membership cancellation request has been processed successfully. Contact support for further assistance. Team GNAT',
        's11_meeting_scheduled' => 'Dear {#var#}, a GNAT meeting is scheduled on {#var#} at {#var#}. Kindly login to the GNAT portal for details. Team GNAT',
        's12_meeting_attendance_confirmed' => 'Dear {#var#}, your attendance confirmation for the GNAT meeting on {#var#} has been submitted successfully. Team GNAT',
        's13_meeting_non_attendance' => 'Dear {#var#}, your non-attendance response for the GNAT meeting on {#var#} has been submitted successfully. Team GNAT',
        's14_new_event' => 'Dear {#var#}, a new event update is available in GNAT Association. Kindly login to the GNAT portal for details. Team GNAT',
        's15_event_interest' => 'Dear {#var#}, your interest for the GNAT event has been recorded successfully. Event updates will be shared through the portal. Team GNAT',
        's16_event_participation' => 'Dear {#var#}, thank you for attending the GNAT event. Your participation has been recorded successfully. Kindly download your participation certificate from the GNAT portal, if applicable. Team GNAT',
        's17_nomination_live' => 'Dear {#var#}, nomination activity is currently available in GNAT Association. Kindly login to the portal to participate. Team GNAT',
        's18_nomination_submitted' => 'Dear {#var#}, your nomination submission has been received successfully in GNAT Association. Team GNAT',
        's19_polling_live' => 'Dear {#var#}, polling activity is currently available in GNAT Association. Kindly submit your response through the portal. Team GNAT',
        's20_polling_results' => 'Dear {#var#}, polling results are now available in GNAT Association. Kindly login to the GNAT portal to view details. Team GNAT',
        's21_polling_response' => 'Dear {#var#}, your polling response has been submitted successfully in GNAT Association. Team GNAT',
        's22_job_posting' => 'Dear {#var#}, a new job posting is available in GNAT Association. Kindly login to the portal to view details. Team GNAT',
        's23_job_application_submitted' => 'Dear {#var#}, your job application has been submitted successfully. Further updates will be shared after review completion. Team GNAT',
        's24_job_request_reviewed' => 'Dear {#var#}, your submitted job request has been reviewed successfully. Further updates will be shared shortly. Team GNAT',
        's25_job_request_status_updated' => 'Dear {#var#}, your job request status has been updated successfully. Further instructions will be shared shortly. Team GNAT',
        's26_job_request_communication' => 'Dear {#var#}, communication has been initiated regarding your submitted job request. Kindly check your registered contact details. Team GNAT',
        's27_donation_received' => 'Dear {#var#}, your donation payment has been received successfully. Thank you for your contribution to GNAT Association. Team GNAT',
        's28_support_request' => 'Dear {#var#}, your support request has been received successfully. Our support team will contact you shortly. Team GNAT',
    ];

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

    public function nominationSubmitted(?string $mobile, string $memberName): void
    {
        $this->sendScenario('s18_nomination_submitted', $mobile, [$memberName]);
    }

    public function pollingLive(?string $mobile, string $memberName): void
    {
        $this->sendScenario('s19_polling_live', $mobile, [$memberName]);
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

    /** Same DLT body as template 25 — used for posted job application status changes. */
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
     * @param  list<string>  $values
     */
    public function renderBody(string $scenarioKey, array $values): string
    {
        $template = self::TEMPLATES[$scenarioKey] ?? '';
        $out = $template;
        foreach ($values as $v) {
            $out = preg_replace('/\{#var#}/', (string) $v, (string) $out, 1) ?? $out;
        }

        return $out;
    }

    /**
     * @param  list<string>  $values
     */
    public function sendScenario(string $scenarioKey, ?string $mobile, array $values): void
    {
        $normalized = $this->normalizeMobile($mobile);
        if ($normalized === null) {
            return;
        }

        $driver = strtolower((string) config('gnat_sms.driver', 'off'));
        if ($driver === 'off' || $driver === '' || $driver === 'false') {
            return;
        }

        $body = $this->renderBody($scenarioKey, $values);

        try {
            if ($driver === 'log') {
                Log::info('GNAT SMS (log driver)', [
                    'scenario' => $scenarioKey,
                    'mobile' => $normalized,
                    'body' => $body,
                ]);

                return;
            }

            if ($driver === 'msg91') {
                $this->sendViaMsg91Flow($scenarioKey, $normalized, $values, $body);

                return;
            }

            Log::warning('GNAT SMS unknown driver', ['driver' => $driver]);
        } catch (\Throwable $e) {
            Log::warning('GNAT SMS send failed', [
                'scenario' => $scenarioKey,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * @param  list<string>  $values
     */
    private function sendViaMsg91Flow(string $scenarioKey, string $normalizedMobile, array $values, string $renderedBody): void
    {
        $authkey = trim((string) config('gnat_sms.authkey', ''));
        if ($authkey === '') {
            Log::warning('GNAT SMS MSG91 authkey missing; set GNAT_MSG91_AUTHKEY');

            return;
        }

        $flowId = config('gnat_sms.flow_ids.'.$scenarioKey);
        $flowId = $flowId !== null && $flowId !== '' ? trim((string) $flowId) : '';

        if ($flowId === '') {
            Log::debug('GNAT SMS MSG91 flow id not configured for scenario; skipping API call', [
                'scenario' => $scenarioKey,
                'preview' => $renderedBody,
            ]);

            return;
        }

        $sender = trim((string) config('gnat_sms.sender', ''));
        $url = rtrim((string) config('gnat_sms.flow_url', 'https://api.msg91.com/api/v5/flow/'), '/').'/';

        $recipient = ['mobiles' => $normalizedMobile];
        foreach (array_values($values) as $i => $val) {
            $recipient['VAR'.($i + 1)] = (string) $val;
        }

        $payload = [
            'flow_id' => $flowId,
            'recipients' => [$recipient],
        ];
        if ($sender !== '') {
            $payload['sender'] = $sender;
        }

        $response = Http::timeout(20)
            ->withHeaders([
                'authkey' => $authkey,
                'Content-Type' => 'application/json',
                'accept' => 'application/json',
            ])
            ->post($url, $payload);

        if (! $response->successful()) {
            Log::warning('GNAT SMS MSG91 HTTP error', [
                'scenario' => $scenarioKey,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        }
    }

    public function normalizeMobile(?string $raw): ?string
    {
        if ($raw === null) {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $raw) ?? '';
        if ($digits === '') {
            return null;
        }

        $cc = preg_replace('/\D+/', '', (string) config('gnat_sms.default_country_code', '91')) ?: '91';

        if (strlen($digits) === 10) {
            return $cc.$digits;
        }

        if (strlen($digits) === 11 && str_starts_with($digits, '0')) {
            return $cc.substr($digits, 1);
        }

        if (strlen($digits) > 10 && str_starts_with($digits, $cc)) {
            return $digits;
        }

        if (strlen($digits) >= 10 && strlen($digits) <= 15) {
            return $digits;
        }

        return null;
    }
}
