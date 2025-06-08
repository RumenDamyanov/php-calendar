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
            'description' => 'Unit test event.'
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
                'mailto:bob@example.com',
            ],
        ]);
        $ics = $calendar->toIcs();
        expect($ics)->toContain('ATTENDEE;CN=mailto:alice@example.com:mailto:alice@example.com');
        expect($ics)->toContain('ATTENDEE;CN=mailto:bob@example.com:mailto:bob@example.com');
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

    it('throws if required fields are missing', function () {
        $calendar = new \Rumenx\Calendar();
        expect(fn() => $calendar->addEvent(['summary' => 'No dates']))->toThrow(\InvalidArgumentException::class);
    });
});
