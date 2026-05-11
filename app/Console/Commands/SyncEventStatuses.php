<?php

namespace App\Console\Commands;

use App\Services\EventScheduleStatusService;
use Illuminate\Console\Command;

class SyncEventStatuses extends Command
{
    protected $signature = 'events:sync-status';

    protected $description = 'Manually recompute all event statuses from dates (the app also syncs on relevant web requests; no cron required)';

    public function handle(EventScheduleStatusService $service): int
    {
        $n = $service->syncAll();
        if ($this->output->isVerbose()) {
            $this->info("Updated {$n} event(s).");
        }

        return self::SUCCESS;
    }
}
