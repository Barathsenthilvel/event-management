<?php

namespace App\Services;

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class EventScheduleStatusService
{
    /**
     * Recompute status from event_dates for all non-cancelled events.
     * Does not change is_active — homepage visibility stays tied to display/active flags.
     *
     * @return int Number of rows updated
     */
    public function syncAll(?Carbon $now = null): int
    {
        $now = $now ?? now();
        $updated = 0;

        Event::query()
            ->where('status', '!=', 'cancelled')
            ->with(['dates:id,event_id,event_date,start_time,end_time'])
            ->orderBy('id')
            ->chunkById(100, function (Collection $events) use ($now, &$updated): void {
                foreach ($events as $event) {
                    if ($this->syncOne($event, $now)) {
                        $updated++;
                    }
                }
            });

        return $updated;
    }

    public function syncOne(Event $event, ?Carbon $now = null): bool
    {
        $now = $now ?? now();

        if ($event->status === 'cancelled') {
            return false;
        }

        $scheduleWindows = $event->dates
            ->filter(fn ($row) => ! empty($row->event_date))
            ->map(function ($row) {
                $eventDay = Carbon::parse($row->event_date)->startOfDay();
                $startTime = $row->start_time ?: '00:00';
                $endTime = $row->end_time ?: '23:59';

                $startAt = Carbon::parse($eventDay->format('Y-m-d').' '.$startTime);
                $endAt = Carbon::parse($eventDay->format('Y-m-d').' '.$endTime);

                if (! empty($row->start_time) && ! empty($row->end_time) && $endAt->lessThanOrEqualTo($startAt)) {
                    $endAt->addDay();
                }

                return [
                    'start' => $startAt,
                    'end' => $endAt,
                ];
            })
            ->values();

        if ($scheduleWindows->isEmpty()) {
            return false;
        }

        $start = $scheduleWindows->min('start');
        $end = $scheduleWindows->max('end');

        if ($now->greaterThan($end)) {
            $newStatus = 'completed';
        } elseif ($now->greaterThanOrEqualTo($start)) {
            $newStatus = 'live';
        } else {
            $newStatus = 'upcoming';
        }

        if ($event->status === $newStatus) {
            return false;
        }

        $event->update(['status' => $newStatus]);

        return true;
    }
}
