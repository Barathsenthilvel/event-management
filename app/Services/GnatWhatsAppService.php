<?php

namespace App\Services;

use App\Models\User;
use App\Services\Concerns\DispatchesGnatNotificationScenarios;
use Illuminate\Support\Facades\Log;

/**
 * GNAT transactional WhatsApp — MSG91 WhatsApp bulk template API.
 *
 * Template keys mirror gnat_sms.php. Set GNAT_WHATSAPP_TEMPLATE_* in .env when
 * the client provides approved WhatsApp template names.
 */
class GnatWhatsAppService
{
    use DispatchesGnatNotificationScenarios;

    /**
     * @return array{status: string, error: string|null}
     */
    public function sendLoginOtp(?string $mobile, string $otp, ?string $memberName = null): array
    {
        $normalized = $this->normalizeMobile($mobile);
        if ($normalized === null) {
            return ['status' => 'skipped', 'error' => 'No mobile number'];
        }

        $name = trim((string) $memberName) !== '' ? trim((string) $memberName) : 'Member';

        $driver = strtolower((string) config('gnat_whatsapp.driver', 'off'));
        if (in_array($driver, ['off', '', 'false'], true)) {
            return ['status' => 'skipped', 'error' => 'WhatsApp driver disabled'];
        }

        if ($driver === 'log') {
            Log::info('GNAT WhatsApp login OTP (log driver)', [
                'mobile' => $normalized,
                'body_1_name' => $name,
                'body_2_otp' => $otp,
            ]);

            return ['status' => 'success', 'error' => null];
        }

        if ($driver !== 'msg91') {
            return ['status' => 'skipped', 'error' => 'Unknown WhatsApp driver: '.$driver];
        }

        try {
            $error = $this->sendViaMsg91('otpauthentication', $normalized, [$name, $otp]);

            return $error === null
                ? ['status' => 'success', 'error' => null]
                : ['status' => 'failed', 'error' => $error];
        } catch (\Throwable $e) {
            Log::warning('GNAT WhatsApp login OTP send failed', [
                'mobile' => $normalized,
                'message' => $e->getMessage(),
            ]);

            return ['status' => 'failed', 'error' => $e->getMessage()];
        }
    }

    public function memberDisplayName(?User $user): string
    {
        return app(GnatSmsService::class)->memberDisplayName($user);
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

        $driver = strtolower((string) config('gnat_whatsapp.driver', 'off'));
        if (in_array($driver, ['off', '', 'false'], true)) {
            return ['status' => 'skipped', 'error' => 'WhatsApp driver disabled'];
        }

        $templateName = $this->resolveTemplateName($scenarioKey);
        $payload = ($templateName !== '')
            ? $this->buildMsg91Payload($templateName, $normalized, $values)
            : null;

        try {
            if ($driver === 'log') {
                Log::info('GNAT WhatsApp (log driver)', [
                    'scenario' => $scenarioKey,
                    'template_key' => config('gnat_whatsapp.scenario_template_keys.'.$scenarioKey),
                    'template_name' => $templateName !== '' ? $templateName : null,
                    'mobile' => $normalized,
                    'payload' => $payload,
                ]);

                return ['status' => 'success', 'error' => null];
            }

            if ($driver === 'msg91') {
                $error = $this->sendViaMsg91Scenario($scenarioKey, $normalized, $values);
                if ($error === null) {
                    return ['status' => 'success', 'error' => null];
                }

                return ['status' => 'failed', 'error' => $error];
            }

            Log::warning('GNAT WhatsApp unknown driver', ['driver' => $driver]);

            return ['status' => 'skipped', 'error' => 'Unknown WhatsApp driver: '.$driver];
        } catch (\Throwable $e) {
            Log::warning('GNAT WhatsApp send failed', [
                'scenario' => $scenarioKey,
                'message' => $e->getMessage(),
            ]);

            return ['status' => 'failed', 'error' => $e->getMessage()];
        }
    }

    private function resolveTemplateName(string $scenarioKey): string
    {
        $templateKey = config('gnat_whatsapp.scenario_template_keys.'.$scenarioKey);
        if (is_string($templateKey) && $templateKey !== '') {
            $name = config('gnat_whatsapp.template_keys.'.$templateKey);
            if ($name !== null && $name !== '') {
                return trim((string) $name);
            }
        }

        return '';
    }

    private function resolveTemplateNameByKey(string $templateKey): string
    {
        $name = config('gnat_whatsapp.template_keys.'.$templateKey);
        if ($name !== null && $name !== '') {
            return trim((string) $name);
        }

        return '';
    }

    /**
     * @param  list<string>  $values
     * @return array<string, mixed>
     */
    private function buildMsg91Payload(string $templateName, string $normalizedMobile, array $values): array
    {
        $components = [];
        foreach (array_values($values) as $i => $val) {
            $components['body_'.($i + 1)] = [
                'type' => 'text',
                'value' => (string) $val,
            ];
        }

        return [
            'integrated_number' => trim((string) config('gnat_whatsapp.integrated_number', '')),
            'content_type' => 'template',
            'payload' => [
                'messaging_product' => 'whatsapp',
                'type' => 'template',
                'template' => [
                    'name' => $templateName,
                    'language' => [
                        'code' => trim((string) config('gnat_whatsapp.language', 'en')),
                        'policy' => 'deterministic',
                    ],
                    'to_and_components' => [
                        [
                            'to' => [$normalizedMobile],
                            'components' => $components,
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @param  list<string>  $values
     */
    private function sendViaMsg91Scenario(string $scenarioKey, string $normalizedMobile, array $values): ?string
    {
        $templateName = $this->resolveTemplateName($scenarioKey);
        if ($templateName === '') {
            Log::debug('GNAT WhatsApp template not configured for scenario; skipping API call', [
                'scenario' => $scenarioKey,
                'template_key' => config('gnat_whatsapp.scenario_template_keys.'.$scenarioKey),
            ]);

            return 'WhatsApp template not configured for scenario';
        }

        return $this->sendViaMsg91($templateName, $normalizedMobile, $values, $scenarioKey);
    }

    /**
     * @param  list<string>  $values
     */
    private function sendViaMsg91(string $templateKeyOrName, string $normalizedMobile, array $values, ?string $scenarioKey = null): ?string
    {
        $authkey = trim((string) config('gnat_whatsapp.authkey', ''));
        if ($authkey === '') {
            Log::warning('GNAT WhatsApp MSG91 authkey missing; set GNAT_MSG91_AUTHKEY');

            return 'MSG91 authkey missing';
        }

        $integratedNumber = trim((string) config('gnat_whatsapp.integrated_number', ''));
        if ($integratedNumber === '') {
            Log::warning('GNAT WhatsApp integrated number missing; set GNAT_WHATSAPP_INTEGRATED_NUMBER');

            return 'WhatsApp integrated number missing';
        }

        $templateName = $scenarioKey !== null
            ? $this->resolveTemplateName($scenarioKey)
            : $this->resolveTemplateNameByKey($templateKeyOrName);

        if ($templateName === '') {
            $templateName = trim($templateKeyOrName);
        }

        if ($templateName === '') {
            return 'WhatsApp template not configured';
        }

        $payload = $this->buildMsg91Payload($templateName, $normalizedMobile, $values);
        $result = $this->postToMsg91($payload);

        if (is_object($result) && isset($result->type) && strtolower((string) $result->type) === 'error') {
            $message = (string) ($result->message ?? 'MSG91 WhatsApp returned error');

            Log::warning('GNAT WhatsApp MSG91 API error', [
                'scenario' => $scenarioKey,
                'template_name' => $templateName,
                'payload' => $payload,
                'response' => $result,
            ]);

            return $message;
        }

        Log::info('GNAT WhatsApp sent via MSG91', [
            'scenario' => $scenarioKey,
            'template_name' => $templateName,
            'mobile' => $normalizedMobile,
            'response' => $result,
        ]);

        return null;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function postToMsg91(array $payload): mixed
    {
        $jsonData = json_encode($payload);
        $url = rtrim((string) config('gnat_whatsapp.bulk_url', ''), '/');
        $authkey = trim((string) config('gnat_whatsapp.authkey', ''));

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

    public function normalizeMobile(?string $raw): ?string
    {
        return app(GnatSmsService::class)->normalizeMobile($raw);
    }
}
