<?php

/**
 * Calendar Interface
 *
 * Defines the contract for calendar event generation and export.
 *
 * @package Rumenx\php-calendar
 */

namespace Rumenx;

interface CalendarInterface
{
    /**
     * Add an event to the calendar.
     *
     * @param array $event Associative array with keys: summary, start, end, location, description, etc.
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
