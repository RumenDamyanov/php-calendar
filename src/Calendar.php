<?php

/**
 * Calendar
 *
 * Main class for generating .ics files for custom events and meetings.
 *
 * @package Rumenx\php-calendar
 */

namespace Rumenx;

class Calendar implements CalendarInterface
{
    /**
     * @var array List of events
     */
    protected array $events = [];

    /**
     * {@inheritdoc}
     */
    public function addEvent(array $event): void
    {
        // Basic validation for required fields
        if (! isset($event['summary'], $event['start'], $event['end'])) {
            throw new \InvalidArgumentException('Event must have summary, start, and end.');
        }
        $this->events[] = $event;
    }

    /**
     * {@inheritdoc}
     */
    public function toIcs(): string
    {
        $ics = "BEGIN:VCALENDAR\r\nVERSION:2.0\r\nPRODID:-//Rumenx//php-calendar//EN\r\n";
        foreach ($this->events as $event) {
            $ics .= $this->formatEvent($event);
        }
        $ics .= "END:VCALENDAR\r\n";

        return $ics;
    }

    /**
     * Format a single event as an ICS VEVENT block.
     *
     * @param array $event
     * @return string
     */
    protected function formatEvent(array $event): string
    {
        $uid = uniqid('', true) . '@php-calendar';
        $dtStart = $this->formatDate($event['start']);
        $dtEnd = $this->formatDate($event['end']);
        $summary = $this->escape($event['summary']);
        $location = isset($event['location']) ? $this->escape($event['location']) : '';
        $description = isset($event['description']) ? $this->escape($event['description']) : '';
        $vevent = "BEGIN:VEVENT\r\n";
        $vevent .= "UID:$uid\r\n";
        $vevent .= "DTSTAMP:" . gmdate('Ymd\THis\Z') . "\r\n";
        $vevent .= "DTSTART:$dtStart\r\n";
        $vevent .= "DTEND:$dtEnd\r\n";
        $vevent .= "SUMMARY:$summary\r\n";
        if ($location !== '') {
            $vevent .= "LOCATION:$location\r\n";
        }
        if ($description !== '') {
            $vevent .= "DESCRIPTION:$description\r\n";
        }
        // Add RRULE for recurrence
        if (isset($event['rrule'])) {
            $vevent .= "RRULE:" . $event['rrule'] . "\r\n";
        }
        // Add attendees
        if (isset($event['attendees']) && is_array($event['attendees'])) {
            foreach ($event['attendees'] as $attendee) {
                $vevent .= "ATTENDEE;CN=" . $this->escape($attendee) . ":$attendee\r\n";
            }
        }
        // Add alarm (VALARM block)
        if (isset($event['alarm']) && is_array($event['alarm'])) {
            $trigger = isset($event['alarm']['trigger'])
                ? $this->escape($event['alarm']['trigger'])
                : '-PT15M';
            $alarmDesc = isset($event['alarm']['description'])
                ? $this->escape($event['alarm']['description'])
                : 'Reminder';
            $vevent .= "BEGIN:VALARM\r\n";
            $vevent .= "TRIGGER:$trigger\r\n";
            $vevent .= "ACTION:DISPLAY\r\n";
            $vevent .= "DESCRIPTION:$alarmDesc\r\n";
            $vevent .= "END:VALARM\r\n";
        }
        $vevent .= "END:VEVENT\r\n";

        return $vevent;
    }

    /**
     * Format a date string to ICS format (UTC).
     *
     * @param string $date
     * @return string
     */
    protected function formatDate(string $date): string
    {
        $dt = new \DateTime($date, new \DateTimeZone('UTC'));

        return $dt->format('Ymd\THis\Z');
    }

    /**
     * Escape text for ICS format.
     *
     * @param string $text
     * @return string
     */
    protected function escape(string $text): string
    {
        // Escape only characters required by RFC 5545: backslash, comma, semicolon, and newlines
        $text = str_replace(['\\', ';', ',', "\n", "\r"], ['\\\\', '\\;', '\\,', '\\n', ''], $text);

        return $text;
    }
}
