<?php

/**
 * Calendar
 *
 * Main class for generating .ics files for custom events and meetings.
 *
 * @package Rumenx\php-calendar
 */

declare(strict_types=1);

namespace Rumenx;

class Calendar implements CalendarInterface
{
    /**
     * @var list<array{
     *     summary: string,
     *     start: string,
     *     end: string,
     *     location?: string,
     *     description?: string,
     *     url?: string,
     *     rrule?: string,
     *     organizer?: string,
     *     attendees?: list<string>,
     *     alarm?: array{trigger: string, description: string}
     * }>
     */
    protected array $events = [];

    /**
     * {@inheritdoc}
     */
    public function addEvent(array $event): void
    {
        $this->events[] = $this->normalizeEvent($event);
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
     * @param array<string, mixed> $event
     * @return array{
     *     summary: string,
     *     start: string,
     *     end: string,
     *     location?: string,
     *     description?: string,
     *     url?: string,
     *     rrule?: string,
     *     organizer?: string,
     *     attendees?: list<string>,
     *     alarm?: array{trigger: string, description: string}
     * }
     */
    protected function normalizeEvent(array $event): array
    {
        foreach (['summary', 'start', 'end'] as $required) {
            if (! isset($event[$required]) || ! is_string($event[$required]) || $event[$required] === '') {
                throw new \InvalidArgumentException('Event must have summary, start, and end.');
            }
        }

        $start = $this->parseDate($event['start']);
        $end = $this->parseDate($event['end']);
        if ($end < $start) {
            throw new \InvalidArgumentException('Event end must not be before the start.');
        }

        $normalized = [
            'summary' => $this->escape($event['summary']),
            'start' => $start->format('Ymd\THis\Z'),
            'end' => $end->format('Ymd\THis\Z'),
        ];

        if (array_key_exists('location', $event)) {
            $location = $this->escape($this->requireString($event['location'], 'Location'));
            if ($location !== '') {
                $normalized['location'] = $location;
            }
        }

        if (array_key_exists('description', $event)) {
            $description = $this->escape($this->requireString($event['description'], 'Description'));
            if ($description !== '') {
                $normalized['description'] = $description;
            }
        }

        if (array_key_exists('url', $event)) {
            $normalized['url'] = $this->requireUri($event['url'], 'URL');
        }

        if (array_key_exists('rrule', $event)) {
            $rrule = $this->requireString($event['rrule'], 'RRULE');
            $this->assertRrule($rrule);
            $normalized['rrule'] = $rrule;
        }

        if (array_key_exists('organizer', $event)) {
            $normalized['organizer'] = $this->requireString($event['organizer'], 'Organizer');
            $this->assertNoControlCharacters($normalized['organizer'], 'Organizer');
        }

        if (array_key_exists('attendees', $event)) {
            if (! is_array($event['attendees'])) {
                throw new \InvalidArgumentException('Attendees must be a list of addresses.');
            }
            $attendees = [];
            foreach ($event['attendees'] as $attendee) {
                if (! is_string($attendee) || $attendee === '') {
                    throw new \InvalidArgumentException('Attendees must be non-empty strings.');
                }
                $this->assertNoControlCharacters($attendee, 'Attendee');
                $attendees[] = $attendee;
            }
            $normalized['attendees'] = $attendees;
        }

        if (array_key_exists('alarm', $event)) {
            if (! is_array($event['alarm'])) {
                throw new \InvalidArgumentException('Alarm settings must be an array.');
            }
            $trigger = $event['alarm']['trigger'] ?? '-PT15M';
            $description = $event['alarm']['description'] ?? 'Reminder';
            if (! is_string($trigger) || ! is_string($description)) {
                throw new \InvalidArgumentException('Alarm trigger and description must be strings.');
            }
            $this->assertTrigger($trigger);
            $normalized['alarm'] = [
                'trigger' => $trigger,
                'description' => $this->escape($description),
            ];
        }

        return $normalized;
    }

    /**
     * @param array{
     *     summary: string,
     *     start: string,
     *     end: string,
     *     location?: string,
     *     description?: string,
     *     url?: string,
     *     rrule?: string,
     *     organizer?: string,
     *     attendees?: list<string>,
     *     alarm?: array{trigger: string, description: string}
     * } $event
     */
    protected function formatEvent(array $event): string
    {
        $lines = [
            'BEGIN:VEVENT',
            'UID:' . uniqid('', true) . '@php-calendar',
            'DTSTAMP:' . gmdate('Ymd\THis\Z'),
            'DTSTART:' . $event['start'],
            'DTEND:' . $event['end'],
            'SUMMARY:' . $event['summary'],
        ];

        if (isset($event['location'])) {
            $lines[] = 'LOCATION:' . $event['location'];
        }
        if (isset($event['description'])) {
            $lines[] = 'DESCRIPTION:' . $event['description'];
        }
        if (isset($event['url'])) {
            $lines[] = 'URL:' . $event['url'];
        }
        if (isset($event['rrule'])) {
            $lines[] = 'RRULE:' . $event['rrule'];
        }
        if (isset($event['organizer'])) {
            $lines[] = $this->addressLine('ORGANIZER', $event['organizer']);
        }
        if (isset($event['attendees'])) {
            foreach ($event['attendees'] as $attendee) {
                $lines[] = $this->addressLine('ATTENDEE', $attendee);
            }
        }
        if (isset($event['alarm'])) {
            $lines[] = 'BEGIN:VALARM';
            $lines[] = 'TRIGGER:' . $event['alarm']['trigger'];
            $lines[] = 'ACTION:DISPLAY';
            $lines[] = 'DESCRIPTION:' . $event['alarm']['description'];
            $lines[] = 'END:VALARM';
        }
        $lines[] = 'END:VEVENT';

        $ics = '';
        foreach ($lines as $line) {
            $ics .= $this->foldLine($line) . "\r\n";
        }

        return $ics;
    }

    protected function parseDate(string $date): \DateTimeImmutable
    {
        try {
            return new \DateTimeImmutable($date, new \DateTimeZone('UTC'));
        } catch (\DateMalformedStringException $exception) {
            throw new \InvalidArgumentException('Event date is not a valid date.', 0, $exception);
        }
    }

    /**
     * Escape text for ICS format (RFC 5545).
     */
    protected function escape(string $text): string
    {
        $text = str_replace('\\', '\\\\', $text);
        $text = str_replace(';', '\\;', $text);
        $text = str_replace(',', '\\,', $text);

        return str_replace(["\r\n", "\r", "\n"], '\\n', $text);
    }

    protected function addressLine(string $name, string $address): string
    {
        $uri = str_starts_with(strtolower($address), 'mailto:') || ! str_contains($address, '@')
            ? $address
            : 'mailto:' . $address;

        return $name . ';CN=' . $this->parameterValue($address) . ':' . $uri;
    }

    protected function parameterValue(string $value): string
    {
        $clean = str_replace('"', "'", $value);
        if (preg_match('/[";:,\\\\ ]/', $clean) === 1) {
            return '"' . $clean . '"';
        }

        return $clean;
    }

    protected function requireString(mixed $value, string $label): string
    {
        if (! is_string($value)) {
            throw new \InvalidArgumentException($label . ' must be a string.');
        }

        return $value;
    }

    protected function requireUri(mixed $value, string $label): string
    {
        $uri = $this->requireString($value, $label);
        if ($uri === '') {
            throw new \InvalidArgumentException($label . ' must not be empty.');
        }
        $this->assertNoControlCharacters($uri, $label);

        return $uri;
    }

    protected function assertNoControlCharacters(string $value, string $label): void
    {
        if (preg_match('/[\x00-\x1F\x7F]/', $value) === 1) {
            throw new \InvalidArgumentException($label . ' contains a control character.');
        }
    }

    protected function assertRrule(string $rrule): void
    {
        $pattern = '/\AFREQ=(?:SECONDLY|MINUTELY|HOURLY|DAILY|WEEKLY|MONTHLY|YEARLY)'
            . '(?:;[A-Z0-9-]+=[A-Za-z0-9,+\-]+)*\z/';
        if (preg_match($pattern, $rrule) !== 1) {
            throw new \InvalidArgumentException('The RRULE value is not a valid recurrence rule.');
        }
    }

    protected function assertTrigger(string $trigger): void
    {
        $duration = '/\A-?P(\d+W|\d+D)?(T(\d+H)?(\d+M)?(\d+S)?)?\z/';
        if (preg_match($duration, $trigger, $matches) === 1) {
            $hasDate = ($matches[1] ?? '') !== '';
            $hasTime = ($matches[3] ?? '') !== '' || ($matches[4] ?? '') !== '' || ($matches[5] ?? '') !== '';
            if ($hasDate || $hasTime) {
                return;
            }
        }

        if (preg_match('/\A\d{8}T\d{6}Z\z/', $trigger) === 1) {
            return;
        }

        throw new \InvalidArgumentException('The alarm trigger must be a duration or a UTC timestamp.');
    }

    /**
     * Fold a content line at 75 octets without splitting a UTF-8 character.
     */
    protected function foldLine(string $line): string
    {
        if (strlen($line) <= 75) {
            return $line;
        }

        $parts = [];
        $offset = 0;
        $limit = 75;
        $length = strlen($line);

        while ($offset < $length) {
            $take = min($limit, $length - $offset);
            if ($offset + $take < $length) {
                while ($take > 0 && (ord($line[$offset + $take]) & 0xC0) === 0x80) {
                    --$take;
                }
                if ($take === 0) {
                    $take = 1;
                }
            }
            $parts[] = substr($line, $offset, $take);
            $offset += $take;
            $limit = 74;
        }

        return implode("\r\n ", $parts);
    }
}
