<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use Throwable;

class MemberIdCardService
{
    /**
     * Format official GNAT member identification code.
     * E.g. GNAT-9715-0004
     */
    public function memberCode(User $user): string
    {
        return 'GNAT-9715-' . str_pad((string) $user->id, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Absolute filesystem paths for member ID card files.
     *
     * @return array{front: string, back: string, combined: string}
     */
    public function getCardPaths(User $user): array
    {
        $dir = storage_path("app/public/id-cards/{$user->id}");

        return [
            'front' => $dir . DIRECTORY_SEPARATOR . 'id_card_front.png',
            'back' => $dir . DIRECTORY_SEPARATOR . 'id_card_back.png',
            'combined' => $dir . DIRECTORY_SEPARATOR . 'id_card_combined.png',
        ];
    }

    /**
     * Resolve the Python binary across Linux, macOS, and Windows.
     */
    public function resolvePythonBinary(): ?string
    {
        $configured = env('PYTHON_BINARY') ?: config('services.python.binary');
        if (!empty($configured) && is_string($configured)) {
            return $configured;
        }

        $candidates = [
            'python3',
            'python',
            '/usr/bin/python3',
            '/usr/local/bin/python3',
            'C:\\Python314\\python.exe',
            'C:\\Python313\\python.exe',
            'C:\\Python312\\python.exe',
            'C:\\Python311\\python.exe',
            'C:\\Python310\\python.exe',
        ];

        $firstWorking = null;
        foreach ($candidates as $cmd) {
            try {
                $process = Process::run([$cmd, '--version']);
                if ($process->successful()) {
                    if (!$firstWorking) {
                        $firstWorking = $cmd;
                    }
                    // Prefer the binary where PIL and qrcode are already installed
                    $depCheck = Process::run([$cmd, '-c', 'import PIL, qrcode; print("OK")']);
                    if ($depCheck->successful() && trim($depCheck->output()) === 'OK') {
                        return $cmd;
                    }
                }
            } catch (Throwable $e) {
                continue;
            }
        }

        return $firstWorking;
    }

    /**
     * Test whether Python and its dependencies (PIL, qrcode) are operational.
     *
     * @return array{ok: bool, binary?: string, error?: string, detail?: string}
     */
    public function testPythonEnvironment(): array
    {
        $binary = $this->resolvePythonBinary();
        if (!$binary) {
            return [
                'ok' => false,
                'error' => 'No Python binary found (tested python3, python, /usr/bin/python3). Please install python3 on the server or specify PYTHON_BINARY in .env.',
            ];
        }

        try {
            $process = Process::run([$binary, '-c', 'import PIL, qrcode; print("OK")']);
            if (!$process->successful() || trim($process->output()) !== 'OK') {
                return [
                    'ok' => false,
                    'binary' => $binary,
                    'error' => 'Python is found, but Pillow or qrcode package is missing. Run: pip3 install Pillow qrcode --break-system-packages',
                    'detail' => $process->errorOutput() ?: $process->output(),
                ];
            }

            return [
                'ok' => true,
                'binary' => $binary,
            ];
        } catch (Throwable $e) {
            return [
                'ok' => false,
                'binary' => $binary,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Public URLs for member ID card files.
     *
     * @return array{front: string, back: string, combined: string}
     */
    public function getCardUrls(User $user): array
    {
        $paths = $this->getCardPaths($user);
        $exists = file_exists($paths['combined']) || file_exists($paths['front']);
        $version = $exists && file_exists($paths['combined']) 
            ? '?' . filemtime($paths['combined']) 
            : ($exists ? '?' . time() : '');

        // If public/storage is not created or inaccessible on server, use Laravel controller preview route
        $storageLinked = file_exists(public_path('storage'));
        if (!$storageLinked && \Illuminate\Support\Facades\Route::has('member.id-card.preview')) {
            $qs = $version ? '&v=' . ltrim($version, '?') : '';
            return [
                'front' => route('member.id-card.preview', ['side' => 'front']) . $qs,
                'back' => route('member.id-card.preview', ['side' => 'back']) . $qs,
                'combined' => route('member.id-card.preview', ['side' => 'combined']) . $qs,
            ];
        }

        return [
            'front' => asset("storage/id-cards/{$user->id}/id_card_front.png") . $version,
            'back' => asset("storage/id-cards/{$user->id}/id_card_back.png") . $version,
            'combined' => asset("storage/id-cards/{$user->id}/id_card_combined.png") . $version,
        ];
    }

    /**
     * Check if ID card files already exist on disk.
     */
    public function cardsExist(User $user): bool
    {
        $paths = $this->getCardPaths($user);

        return file_exists($paths['front']) && file_exists($paths['back']) && file_exists($paths['combined']);
    }

    /**
     * Ensure the ID card images are generated on disk.
     * If already generated and not forced, returns existing paths immediately.
     *
     * @return array{front: string, back: string, combined: string}|null
     */
    public function ensureCardGenerated(User $user, bool $force = false): ?array
    {
        $paths = $this->getCardPaths($user);

        if (!$force && $this->cardsExist($user)) {
            return $paths;
        }

        try {
            $user->loadMissing('designation');

            $templateFront = public_path('images/id-card/template_front.png');
            $templateBack = public_path('images/id-card/template_back.png');

            if (!file_exists($templateFront) || !file_exists($templateBack)) {
                Log::error('MemberIdCardService: Templates missing in public/images/id-card', [
                    'templateFront' => $templateFront,
                    'templateBack' => $templateBack,
                ]);
                return null;
            }

            $outputDir = storage_path("app/public/id-cards/{$user->id}");
            File::ensureDirectoryExists($outputDir);

            // Resolve photo if available
            $photoPath = null;
            if ($user->passport_photo_path) {
                $candidate = Storage::disk('public')->path($user->passport_photo_path);
                if (file_exists($candidate)) {
                    $photoPath = $candidate;
                }
            }

            $designation = $user->designation?->name;
            if (empty($designation)) {
                $designation = !empty($user->profile_type) ? (string) $user->profile_type : 'MEMBER';
            }

            $memberCode = $this->memberCode($user);
            $verificationUrl = url("/verify-member/{$memberCode}");

            $payload = [
                'template_front' => $templateFront,
                'template_back' => $templateBack,
                'output_dir' => $outputDir,
                'name' => (string) ($user->name ?? 'GNAT Member'),
                'designation' => $designation,
                'member_id' => $memberCode,
                'photo_path' => $photoPath,
                'blood_group' => (string) ($user->blood_group ?? '—'),
                'mobile' => (string) ($user->mobile ?? '—'),
                'rnrm_no' => (string) ($user->rnrm_number_with_date ?? '—'),
                'qr_data' => $verificationUrl,
            ];

            $tempDir = storage_path('app/temp');
            File::ensureDirectoryExists($tempDir);
            $tempFile = $tempDir . DIRECTORY_SEPARATOR . 'id_card_payload_' . $user->id . '_' . uniqid() . '.json';
            file_put_contents($tempFile, json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

            $python = $this->resolvePythonBinary();
            if (!$python) {
                Log::error('MemberIdCardService: No Python binary found. Ensure python3 is installed on the server or set PYTHON_BINARY in .env');
                return $this->cardsExist($user) ? $paths : null;
            }

            $scriptPath = base_path('app/Scripts/generate_id_card.py');

            try {
                $result = Process::path(base_path())
                    ->timeout(30)
                    ->run([$python, $scriptPath, '--payload-file', $tempFile]);

                if (!$result->successful()) {
                    Log::error('MemberIdCardService: Python script failed', [
                        'binary' => $python,
                        'exitCode' => $result->exitCode(),
                        'errorOutput' => $result->errorOutput(),
                        'output' => $result->output(),
                    ]);
                    return $this->cardsExist($user) ? $paths : null;
                }

                $json = json_decode($result->output(), true);
                if (!is_array($json) || empty($json['success'])) {
                    Log::error('MemberIdCardService: Generation script reported failure', [
                        'response' => $result->output(),
                    ]);
                    return $this->cardsExist($user) ? $paths : null;
                }

                return $paths;
            } finally {
                if (file_exists($tempFile)) {
                    @unlink($tempFile);
                }
            }
        } catch (Throwable $e) {
            Log::error('MemberIdCardService: Exception while generating card', [
                'userId' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return $this->cardsExist($user) ? $paths : null;
        }
    }

    /**
     * Get email attachment(s) for the member ID card.
     *
     * @return list<Attachment>
     */
    public function getMailAttachments(User $user): array
    {
        $paths = $this->ensureCardGenerated($user);

        if (!$paths) {
            return [];
        }

        $attachments = [];
        $memberCode = $this->memberCode($user);

        if (file_exists($paths['combined'])) {
            $attachments[] = Attachment::fromPath($paths['combined'])
                ->as("GNAT-ID-Card-{$memberCode}.png")
                ->withMime('image/png');
        } elseif (file_exists($paths['front'])) {
            $attachments[] = Attachment::fromPath($paths['front'])
                ->as("GNAT-ID-Card-Front-{$memberCode}.png")
                ->withMime('image/png');
        }

        return $attachments;
    }
}

