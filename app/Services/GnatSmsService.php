<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;

/**
 * GNAT transactional SMS — MSG91 Flow API (flow_id + var1/var2 from config).
 */
class GnatSmsService
{
    /**
     * @return array{status: string, error: string|null}
     */
    public function sendLoginOtp(?string $mobile, string $otp): array
    {
        $normalized = $this->normalizeMobile($mobile);
        if ($normalized === null) {
            return ['status' => 'skipped', 'error' => 'No mobile number'];
        }

        $driver = strtolower((string) config('gnat_sms.driver', 'off'));
        if (in_array($driver, ['off', '', 'false'], true)) {
            return ['status' => 'skipped', 'error' => 'SMS driver disabled'];
        }

        if ($driver === 'log') {
            Log::info('GNAT SMS login OTP (log driver)', [
                'mobile' => $normalized,
                'otp' => $otp,
            ]);

            return ['status' => 'success', 'error' => null];
        }

        if ($driver !== 'msg91') {
            return ['status' => 'skipped', 'error' => 'Unknown SMS driver: '.$driver];
        }

        try {
            $error = $this->sendLoginOtpViaMsg91($normalized, $otp);

            return $error === null
                ? ['status' => 'success', 'error' => null]
                : ['status' => 'failed', 'error' => $error];
        } catch (\Throwable $e) {
            Log::warning('GNAT SMS login OTP send failed', [
                'mobile' => $normalized,
                'message' => $e->getMessage(),
            ]);

            return ['status' => 'failed', 'error' => $e->getMessage()];
        }
    }

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
     * @return array<string, mixed>
     */
    private function buildMsg91Payload(string $flowId, string $normalizedMobile, array $values): array
    {
        $payload = [
            'flow_id' => $flowId,
            'mobiles' => $normalizedMobile,
        ];

        foreach (array_values($values) as $i => $val) {
            $payload['var'.($i + 1)] = (string) $val;
        }

        $sender = trim((string) config('gnat_sms.sender', ''));
        if ($sender !== '') {
            $payload['sender'] = $sender;
        }

        return $payload;
    }

    /**
     * @param  list<string>  $values
     */
    public function sendScenario(string $scenarioKey, ?string $mobile, array $values): void
    {
        $this->trySendScenario($scenarioKey, $mobile, $values);
    }

    /**
     * @param  list<string>  $values
     * @return array{status: string, error: string|null}
     */
    public function trySendScenario(string $scenarioKey, ?string $mobile, array $values): array
    {
        $normalized = $this->normalizeMobile($mobile);
        if ($normalized === null) {
            return ['status' => 'skipped', 'error' => 'No mobile number'];
        }

        $driver = strtolower((string) config('gnat_sms.driver', 'off'));
        if (in_array($driver, ['off', '', 'false'], true)) {
            return ['status' => 'skipped', 'error' => 'SMS driver disabled'];
        }

        $flowId = $this->resolveFlowId($scenarioKey);
        $payload = ($flowId !== '')
            ? $this->buildMsg91Payload($flowId, $normalized, $values)
            : null;

        try {
            if ($driver === 'log') {
                Log::info('GNAT SMS (log driver)', [
                    'scenario' => $scenarioKey,
                    'template_key' => config('gnat_sms.scenario_template_keys.'.$scenarioKey),
                    'flow_id' => $flowId !== '' ? $flowId : null,
                    'mobile' => $normalized,
                    'payload' => $payload,
                ]);

                return ['status' => 'success', 'error' => null];
            }

            if ($driver === 'msg91') {
                $error = $this->sendViaMsg91Flow($scenarioKey, $normalized, $values);
                if ($error === null) {
                    return ['status' => 'success', 'error' => null];
                }

                return ['status' => 'failed', 'error' => $error];
            }

            Log::warning('GNAT SMS unknown driver', ['driver' => $driver]);

            return ['status' => 'skipped', 'error' => 'Unknown SMS driver: '.$driver];
        } catch (\Throwable $e) {
            Log::warning('GNAT SMS send failed', [
                'scenario' => $scenarioKey,
                'message' => $e->getMessage(),
            ]);

            return ['status' => 'failed', 'error' => $e->getMessage()];
        }
    }

    private function resolveFlowId(string $scenarioKey): string
    {
        $templateKey = config('gnat_sms.scenario_template_keys.'.$scenarioKey);
        if (is_string($templateKey) && $templateKey !== '') {
            $flowId = config('gnat_sms.template_keys.'.$templateKey);
            if ($flowId !== null && $flowId !== '') {
                return trim((string) $flowId);
            }
        }

        return '';
    }

    /**
     * MSG91 Flow API — same curl pattern as GNAT server backup (curl.php).
     *
     * @param  array<string, mixed>  $payload  e.g. flow_id, mobiles, var1, var2...
     */
    private function sendLoginOtpViaMsg91(string $normalizedMobile, string $otp): ?string
    {
        $authkey = trim((string) config('gnat_sms.authkey', ''));
        if ($authkey === '') {
            Log::warning('GNAT SMS MSG91 authkey missing; set GNAT_MSG91_AUTHKEY');

            return 'MSG91 authkey missing';
        }

        $flowId = trim((string) config('gnat_sms.otp_flow_id', ''));
        if ($flowId === '') {
            $flowId = trim((string) config('gnat_sms.template_keys.otpauthentication', ''));
        }
        if ($flowId !== '') {
            return $this->sendLoginOtpViaMsg91Flow($flowId, $normalizedMobile, $otp);
        }

        $templateId = trim((string) config('gnat_sms.otp_template_id', ''));
        if ($templateId === '') {
            $templateId = trim((string) config('gnat_sms.template_keys.otpauthentication', ''));
        }
        if ($templateId === '') {
            return 'MSG91 OTP template not configured';
        }

        $baseUrl = rtrim((string) config('gnat_sms.otp_url', 'https://control.msg91.com/api/v5/otp'), '/');
        $url = $baseUrl.'?'.http_build_query([
            'template_id' => $templateId,
            'mobile' => $normalizedMobile,
        ]);

        $payload = [
            'otp' => $otp,
            'otp_length' => strlen($otp),
            'otp_expiry' => 5,
        ];

        $sender = trim((string) config('gnat_sms.sender', ''));
        if ($sender !== '') {
            $payload['sender'] = $sender;
        }

        $jsonData = json_encode($payload);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'authkey: '.$authkey,
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode((string) $response);
        if (is_object($result) && isset($result->type) && strtolower((string) $result->type) === 'error') {
            $message = (string) ($result->message ?? 'MSG91 OTP API returned error');

            Log::warning('GNAT SMS MSG91 OTP API error', [
                'mobile' => $normalizedMobile,
                'template_id' => $templateId,
                'response' => $result,
            ]);

            return $message;
        }

        Log::info('GNAT SMS login OTP sent via MSG91 OTP API', [
            'mobile' => $normalizedMobile,
            'template_id' => $templateId,
            'response' => $result,
        ]);

        return null;
    }

    private function sendLoginOtpViaMsg91Flow(string $flowId, string $normalizedMobile, string $otp): ?string
    {
        $payload = $this->buildMsg91Payload($flowId, $normalizedMobile, [$otp]);
        $result = $this->sendTextSms($payload);

        if (is_object($result) && isset($result->type) && strtolower((string) $result->type) === 'error') {
            $message = (string) ($result->message ?? 'MSG91 returned error');

            Log::warning('GNAT SMS MSG91 OTP flow error', [
                'flow_id' => $flowId,
                'mobile' => $normalizedMobile,
                'response' => $result,
            ]);

            return $message;
        }

        Log::info('GNAT SMS login OTP sent via MSG91 Flow', [
            'flow_id' => $flowId,
            'mobile' => $normalizedMobile,
            'response' => $result,
        ]);

        return null;
    }

    public function sendTextSms(array $payload): mixed
    {
        $jsonData = json_encode($payload);
        $url = rtrim((string) config('gnat_sms.flow_url', 'https://control.msg91.com/api/v5/flow'), '/');
        $authkey = trim((string) config('gnat_sms.authkey', ''));

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'authkey: '.$authkey,
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode((string) $response);
    }

    /**
     * Build MSG91 payload and send — flow_id + mobiles + var1/var2/... dynamically.
     *
     * @param  list<string>  $values
     * @return ?string Error message or null on success
     */
    private function sendViaMsg91Flow(string $scenarioKey, string $normalizedMobile, array $values): ?string
    {
        $authkey = trim((string) config('gnat_sms.authkey', ''));
        if ($authkey === '') {
            Log::warning('GNAT SMS MSG91 authkey missing; set GNAT_MSG91_AUTHKEY');

            return 'MSG91 authkey missing';
        }

        $flowId = $this->resolveFlowId($scenarioKey);
        if ($flowId === '') {
            Log::debug('GNAT SMS MSG91 flow id not configured for scenario; skipping API call', [
                'scenario' => $scenarioKey,
                'template_key' => config('gnat_sms.scenario_template_keys.'.$scenarioKey),
            ]);

            return 'MSG91 flow id not configured for scenario';
        }

        $payload = $this->buildMsg91Payload($flowId, $normalizedMobile, $values);
        $result = $this->sendTextSms($payload);

        if (is_object($result) && isset($result->type) && strtolower((string) $result->type) === 'error') {
            $message = (string) ($result->message ?? 'MSG91 returned error');

            Log::warning('GNAT SMS MSG91 API error', [
                'scenario' => $scenarioKey,
                'flow_id' => $flowId,
                'payload' => $payload,
                'response' => $result,
            ]);

            return $message;
        }

        Log::info('GNAT SMS sent via MSG91', [
            'scenario' => $scenarioKey,
            'flow_id' => $flowId,
            'mobiles' => $normalizedMobile,
            'response' => $result,
        ]);

        return null;
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