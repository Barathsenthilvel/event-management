<?php

namespace App\Jobs;

use App\Models\AdminJob;
use App\Models\Event;
use App\Models\GnatNotificationBatch;
use App\Models\Meeting;
use App\Models\Nomination;
use App\Models\Polling;
use App\Services\GnatMailService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Sends a chunk of member notifications (email / SMS / WhatsApp-SMS) in the background
 * so admin invite/alert forms return immediately.
 */
class SendGnatBulkNotificationChunkJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public const TYPE_EVENT_INVITES = 'event_invites';

    public const TYPE_EVENT_INVITE_REMINDERS = 'event_invite_reminders';

    public const TYPE_MEETING_INVITES = 'meeting_invites';

    public const TYPE_MEETING_INVITE_REMINDERS = 'meeting_invite_reminders';

    public const TYPE_NOMINATION_ALERTS = 'nomination_alerts';

    public const TYPE_JOB_POSTING_ALERTS = 'job_posting_alerts';

    public const TYPE_POLLING_LIVE_ALERTS = 'polling_live_alerts';

    public const TYPE_POLLING_RESULTS_ALERTS = 'polling_results_alerts';

    public int $timeout = 180;

    public int $tries = 2;

    /**
     * @param  list<int>  $userIds
     */
    public function __construct(
        public string $type,
        public int $entityId,
        public array $userIds,
        public bool $notifyEmail = true,
        public bool $notifySms = false,
        public bool $notifyWhatsApp = false,
        public ?int $broadcastBatchId = null,
    ) {}

    /**
     * @param  list<int>  $userIds
     */
    public static function dispatchChunks(
        string $type,
        int $entityId,
        array $userIds,
        bool $notifyEmail = true,
        bool $notifySms = false,
        bool $notifyWhatsApp = false,
        int $chunkSize = 200,
        ?int $broadcastBatchId = null,
    ): void {
        if ($userIds === []) {
            return;
        }
        foreach (array_chunk($userIds, $chunkSize) as $chunk) {
            self::dispatch($type, $entityId, array_values($chunk), $notifyEmail, $notifySms, $notifyWhatsApp, $broadcastBatchId);
        }
    }

    public function handle(GnatMailService $mail): void
    {
        if ($this->userIds === []) {
            $this->finishChunk();

            return;
        }

        match ($this->type) {
            self::TYPE_EVENT_INVITES => $this->runEventInvites($mail),
            self::TYPE_EVENT_INVITE_REMINDERS => $this->runEventInviteReminders($mail),
            self::TYPE_MEETING_INVITES => $this->runMeetingInvites($mail),
            self::TYPE_MEETING_INVITE_REMINDERS => $this->runMeetingInviteReminders($mail),
            self::TYPE_NOMINATION_ALERTS => $this->runNominationAlerts($mail),
            self::TYPE_JOB_POSTING_ALERTS => $this->runJobPostingAlerts($mail),
            self::TYPE_POLLING_LIVE_ALERTS => $this->runPollingLiveAlerts($mail),
            self::TYPE_POLLING_RESULTS_ALERTS => $this->runPollingResultsAlerts($mail),
            default => Log::warning('SendGnatBulkNotificationChunkJob: unknown type', ['type' => $this->type]),
        };

        $this->finishChunk();
    }

    public function failed(?Throwable $exception): void
    {
        Log::error('SendGnatBulkNotificationChunkJob failed', [
            'type' => $this->type,
            'entity_id' => $this->entityId,
            'user_count' => count($this->userIds),
            'message' => $exception?->getMessage(),
        ]);

        if ($this->broadcastBatchId !== null) {
            GnatNotificationBatch::query()->find($this->broadcastBatchId)?->markFailed($exception?->getMessage());
        }
    }

    private function finishChunk(): void
    {
        if ($this->broadcastBatchId === null) {
            return;
        }

        GnatNotificationBatch::query()->find($this->broadcastBatchId)?->markChunkFinished();
    }

    private function runEventInvites(GnatMailService $mail): void
    {
        $event = Event::query()->find($this->entityId);
        if (! $event) {
            return;
        }
        $mail->sendEventInvites($event, $this->userIds, $this->broadcastBatchId);
    }

    private function runEventInviteReminders(GnatMailService $mail): void
    {
        $event = Event::query()->find($this->entityId);
        if (! $event) {
            return;
        }
        $mail->sendEventInviteReminders(
            $event,
            $this->userIds,
            $this->broadcastBatchId,
            $this->notifyEmail,
            $this->notifySms,
            $this->notifyWhatsApp
        );
    }

    private function runMeetingInvites(GnatMailService $mail): void
    {
        $meeting = Meeting::query()->find($this->entityId);
        if (! $meeting) {
            return;
        }
        $mail->sendMeetingInvites($meeting, $this->userIds, $this->broadcastBatchId);
    }

    private function runMeetingInviteReminders(GnatMailService $mail): void
    {
        $meeting = Meeting::query()->find($this->entityId);
        if (! $meeting) {
            return;
        }
        $mail->sendMeetingInviteReminders(
            $meeting,
            $this->userIds,
            $this->broadcastBatchId,
            $this->notifyEmail,
            $this->notifySms,
            $this->notifyWhatsApp
        );
    }

    private function runNominationAlerts(GnatMailService $mail): void
    {
        $nomination = Nomination::query()->find($this->entityId);
        if (! $nomination) {
            return;
        }
        $mail->sendNominationAlerts($nomination, $this->userIds, $this->broadcastBatchId);
    }

    private function runJobPostingAlerts(GnatMailService $mail): void
    {
        $job = AdminJob::query()->find($this->entityId);
        if (! $job) {
            return;
        }
        $mail->sendJobPostingAlerts($job, $this->userIds, $this->broadcastBatchId);
    }

    private function runPollingLiveAlerts(GnatMailService $mail): void
    {
        $polling = Polling::query()->find($this->entityId);
        if (! $polling) {
            return;
        }
        $mail->sendPollingLiveAlerts(
            $polling,
            $this->userIds,
            $this->notifyEmail,
            $this->notifySms,
            $this->notifyWhatsApp,
            $this->broadcastBatchId
        );
    }

    private function runPollingResultsAlerts(GnatMailService $mail): void
    {
        $polling = Polling::query()->find($this->entityId);
        if (! $polling) {
            return;
        }
        $mail->sendPollingResultsPublishedAlerts(
            $polling,
            $this->userIds,
            $this->notifyEmail,
            $this->notifySms,
            $this->notifyWhatsApp,
            $this->broadcastBatchId
        );
    }
}
