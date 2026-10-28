<?php

/**
 * Calendar Test
 *
 * Tests for the Calendar class to ensure correct .ics generation and event handling.
 *
 * @package Rumenx\php-calendar
 */

describe('Calendar', function () {
    it('can add an event and export as ICS', function () {
        $calendar = new \Rumenx\Calendar();
        $calendar->addEvent([
            'summary' => 'Test Event',
            'start' => '2025-06-10 10:00:00',
            'end' => '2025-06-10 11:00:00',
            'location' => 'HQ',
            'description' => 'Unit test event.',
        ]);
        $ics = $calendar->toIcs();
        expect($ics)->toContain('BEGIN:VEVENT');
        expect($ics)->toContain('SUMMARY:Test Event');
        expect($ics)->toContain('LOCATION:HQ');
        expect($ics)->toContain('DESCRIPTION:Unit test event.');
        expect($ics)->toContain('END:VEVENT');
    });

    it('can add a recurring event (RRULE)', function () {
        $calendar = new \Rumenx\Calendar();
        $calendar->addEvent([
            'summary' => 'Weekly Sync',
            'start' => '2025-06-12 10:00:00',
            'end' => '2025-06-12 11:00:00',
            'rrule' => 'FREQ=WEEKLY;BYDAY=TH;COUNT=10',
        ]);
        $ics = $calendar->toIcs();
        expect($ics)->toContain('RRULE:FREQ=WEEKLY;BYDAY=TH;COUNT=10');
    });

    it('can add attendees to an event', function () {
        $calendar = new \Rumenx\Calendar();
        $calendar->addEvent([
            'summary' => 'Kickoff',
            'start' => '2025-06-15 09:00:00',
            'end' => '2025-06-15 10:30:00',
            'attendees' => [
                'mailto:alice@example.com',
                'carol@example.com',
            ],
        ]);
        $ics = $calendar->toIcs();
        expect($ics)->toContain('ATTENDEE;CN="mailto:alice@example.com":mailto:alice@example.com');
        expect($ics)->toContain('ATTENDEE;CN=carol@example.com:mailto:carol@example.com');
    });

    it('can add an alarm/VALARM to an event', function () {
        $calendar = new \Rumenx\Calendar();
        $calendar->addEvent([
            'summary' => 'Doctor Appointment',
            'start' => '2025-06-20 15:00:00',
            'end' => '2025-06-20 15:30:00',
            'alarm' => [
                'trigger' => '-PT15M',
                'description' => 'Appointment Reminder',
            ],
        ]);
        $ics = $calendar->toIcs();
        expect($ics)->toContain('BEGIN:VALARM');
        expect($ics)->toContain('TRIGGER:-PT15M');
        expect($ics)->toContain('DESCRIPTION:Appointment Reminder');
        expect($ics)->toContain('END:VALARM');
    });

    it('can add an alarm with default values', function () {
        $calendar = new \Rumenx\Calendar();
        $calendar->addEvent([
            'summary' => 'Meeting with defaults',
            'start' => '2025-06-21 10:00:00',
            'end' => '2025-06-21 11:00:00',
            'alarm' => [], // Empty alarm array to trigger defaults
        ]);
        $ics = $calendar->toIcs();
        expect($ics)->toContain('BEGIN:VALARM');
        expect($ics)->toContain('TRIGGER:-PT15M'); // Default trigger
        expect($ics)->toContain('DESCRIPTION:Reminder'); // Default description
        expect($ics)->toContain('END:VALARM');
    });

    it('can add an alarm with partial values (missing trigger)', function () {
        $calendar = new \Rumenx\Calendar();
        $calendar->addEvent([
            'summary' => 'Meeting with partial alarm',
            'start' => '2025-06-22 14:00:00',
            'end' => '2025-06-22 15:00:00',
            'alarm' => [
                'description' => 'Custom reminder text',
            ],
        ]);
        $ics = $calendar->toIcs();
        expect($ics)->toContain('TRIGGER:-PT15M'); // Default trigger
        expect($ics)->toContain('DESCRIPTION:Custom reminder text');
    });

    it('can add an alarm with partial values (missing description)', function () {
        $calendar = new \Rumenx\Calendar();
        $calendar->addEvent([
            'summary' => 'Meeting with partial alarm',
            'start' => '2025-06-22 16:00:00',
            'end' => '2025-06-22 17:00:00',
            'alarm' => [
                'trigger' => '-PT30M',
            ],
        ]);
        $ics = $calendar->toIcs();
        expect($ics)->toContain('TRIGGER:-PT30M');
        expect($ics)->toContain('DESCRIPTION:Reminder'); // Default description
    });

    it('throws if required fields are missing', function () {
        $calendar = new \Rumenx\Calendar();
        expect(fn () => $calendar->addEvent(['summary' => 'No dates']))->toThrow(\InvalidArgumentException::class);
    });

    it('escapes text and writes url and organizer', function () {
        $calendar = new \Rumenx\Calendar();
        $calendar->addEvent([
            'summary' => "Hello\r\nLOCATION:injected",
            'start' => '2025-06-10 10:00:00',
            'end' => '2025-06-10 11:00:00',
            'location' => 'Room A, B; West',
            'description' => "Line\nTwo",
            'url' => 'https://example.com/a,b',
            'organizer' => 'ada@example.com',
        ]);
        $ics = $calendar->toIcs();

        expect($ics)->toContain('SUMMARY:Hello\\nLOCATION:injected');
        expect($ics)->not->toMatch("/\r\nLOCATION:injected/");
        expect($ics)->toContain('LOCATION:Room A\\, B\\; West');
        expect($ics)->toContain('DESCRIPTION:Line\\nTwo');
        expect($ics)->toContain('URL:https://example.com/a,b');
        expect($ics)->toContain('ORGANIZER;CN=ada@example.com:mailto:ada@example.com');
    });

    it('escapes a bare carriage return and a backslash', function () {
        $calendar = new \Rumenx\Calendar();
        $calendar->addEvent([
            'summary' => "A\\B\rC",
            'start' => '2025-06-10 10:00:00',
            'end' => '2025-06-10 11:00:00',
            'location' => '',
        ]);
        $ics = $calendar->toIcs();

        expect($ics)->toContain('SUMMARY:A\\\\B\\nC');
        expect($ics)->not->toContain('LOCATION:');
    });

    it('rejects dates, rules, addresses, and alarms that would break the file', function () {
        $calendar = new \Rumenx\Calendar();
        $base = [
            'summary' => 'Meeting',
            'start' => '2025-06-10 10:00:00',
            'end' => '2025-06-10 11:00:00',
        ];

        expect(fn () => $calendar->addEvent([
            'summary' => 'Meeting',
            'start' => '2025-06-10 12:00:00',
            'end' => '2025-06-10 11:00:00',
        ]))->toThrow(\InvalidArgumentException::class);
        expect(fn () => $calendar->addEvent([
            'summary' => 'Meeting',
            'start' => 'not-a-date',
            'end' => '2025-06-10 11:00:00',
        ]))->toThrow(\InvalidArgumentException::class);
        expect(fn () => $calendar->addEvent($base + [
            'rrule' => "FREQ=DAILY\nSUMMARY:injected",
        ]))->toThrow(\InvalidArgumentException::class);
        expect(fn () => $calendar->addEvent($base + [
            'rrule' => ['FREQ=DAILY'],
        ]))->toThrow(\InvalidArgumentException::class);
        expect(fn () => $calendar->addEvent($base + [
            'url' => "https://example.com\r\nSUMMARY:injected",
        ]))->toThrow(\InvalidArgumentException::class);
        expect(fn () => $calendar->addEvent($base + [
            'url' => '',
        ]))->toThrow(\InvalidArgumentException::class);
        expect(fn () => $calendar->addEvent($base + [
            'url' => ['https://example.com'],
        ]))->toThrow(\InvalidArgumentException::class);
        expect(fn () => $calendar->addEvent($base + [
            'location' => ['HQ'],
        ]))->toThrow(\InvalidArgumentException::class);
        expect(fn () => $calendar->addEvent($base + [
            'organizer' => "ada@example.com\nSUMMARY:injected",
        ]))->toThrow(\InvalidArgumentException::class);
        expect(fn () => $calendar->addEvent($base + [
            'attendees' => "ada@example.com",
        ]))->toThrow(\InvalidArgumentException::class);
        expect(fn () => $calendar->addEvent($base + [
            'attendees' => [''],
        ]))->toThrow(\InvalidArgumentException::class);
        expect(fn () => $calendar->addEvent($base + [
            'attendees' => ["ada@example.com\r\nSUMMARY:injected"],
        ]))->toThrow(\InvalidArgumentException::class);
        expect(fn () => $calendar->addEvent($base + [
            'alarm' => '-PT15M',
        ]))->toThrow(\InvalidArgumentException::class);
        expect(fn () => $calendar->addEvent($base + [
            'alarm' => ['trigger' => 15],
        ]))->toThrow(\InvalidArgumentException::class);
        expect(fn () => $calendar->addEvent($base + [
            'alarm' => ['trigger' => "-PT15M\nACTION:EMAIL"],
        ]))->toThrow(\InvalidArgumentException::class);
        expect(fn () => $calendar->addEvent($base + [
            'alarm' => ['description' => ['Reminder']],
        ]))->toThrow(\InvalidArgumentException::class);
    });

    it('accepts a date duration and a UTC alarm trigger', function () {
        $calendar = new \Rumenx\Calendar();
        $calendar->addEvent([
            'summary' => 'All day',
            'start' => '2025-06-10 00:00:00',
            'end' => '2025-06-11 00:00:00',
            'alarm' => ['trigger' => '-P1D', 'description' => 'Tomorrow'],
        ]);
        $calendar->addEvent([
            'summary' => 'Stamp',
            'start' => '2025-06-10 00:00:00',
            'end' => '2025-06-11 00:00:00',
            'alarm' => ['trigger' => '20250610T090000Z'],
        ]);
        $ics = $calendar->toIcs();

        expect($ics)->toContain('TRIGGER:-P1D');
        expect($ics)->toContain('TRIGGER:20250610T090000Z');
    });

    it('folds long lines on octet boundaries and keeps UTF-8 characters intact', function () {
        $note = str_repeat('a', 66) . 'é' . str_repeat('b', 20);
        $calendar = new \Rumenx\Calendar();
        $calendar->addEvent([
            'summary' => $note,
            'start' => '2025-06-10 10:00:00',
            'end' => '2025-06-10 11:00:00',
            'description' => str_repeat("\x80", 80),
        ]);
        $ics = $calendar->toIcs();
        $unfolded = preg_replace("/\r\n[ \t]/", '', $ics);

        expect($ics)->toContain("\r\n ");
        expect($unfolded)->toContain('SUMMARY:' . $note);
        expect($unfolded)->toContain('DESCRIPTION:' . str_repeat("\x80", 80));
    });
});
