<?php

/**
 * Calendar Interface
 *
 * Defines the contract for calendar event generation and export.
 *
 * @package Rumenx\php-calendar
 */

declare(strict_types=1);

namespace Rumenx;

interface CalendarInterface
{
    /**
     * Add an event to the calendar.
     *
     * @param array<string, mixed> $event Event fields: summary, start, end,
     *        and optional location, description, url, rrule, organizer, attendees, alarm.
     * @return void
     */
    public function addEvent(array $event): void;

    /**
     * Export the calendar as an ICS string.
     *
     * @return string
     */
    public function toIcs(): string;
}
