<?php

namespace App\Support;

final class EventInterestErrorFlash
{
    public const ERR_ENDED = 'EVENT_INTEREST_ENDED';

    public const ERR_SEAT_LIMIT = 'EVENT_INTEREST_SEAT_LIMIT';

    /**
     * @return array{event_interest_error_title: string, event_interest_error: string}
     */
    public static function eventEnded(): array
    {
        return [
            'event_interest_error_title' => 'Event has ended',
            'event_interest_error' => "**Registration is closed** for this event.\n\nThe scheduled date and time have passed, so we are **not accepting new interest**.",
        ];
    }

    /**
     * @return array{event_interest_error_title: string, event_interest_error: string}
     */
    public static function seatLimit(): array
    {
        return [
            'event_interest_error_title' => 'Seats full',
            'event_interest_error' => "**All available seats are taken.**\n\nThis event has reached its registration limit. Thank you for your interest.",
        ];
    }

    /**
     * @return array{event_interest_error_title: string, event_interest_error: string}
     */
    public static function fromException(\RuntimeException $e): array
    {
        return match ($e->getMessage()) {
            self::ERR_ENDED => self::eventEnded(),
            self::ERR_SEAT_LIMIT => self::seatLimit(),
            default => [
                'event_interest_error_title' => 'Event registration unavailable',
                'event_interest_error' => $e->getMessage(),
            ],
        };
    }
}
