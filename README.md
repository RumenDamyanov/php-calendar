# php-calendar

[![CI](https://github.com/RumenDamyanov/php-calendar/actions/workflows/ci.yml/badge.svg?branch=master)](https://github.com/RumenDamyanov/php-calendar/actions/workflows/ci.yml)
[![codecov](https://codecov.io/gh/RumenDamyanov/php-calendar/branch/master/graph/badge.svg)](https://codecov.io/gh/RumenDamyanov/php-calendar)

A modern, framework-agnostic PHP package to generate RFC 5545-compliant .ics files for custom events and meetings. Easily integrates with any PHP project, including Laravel and Symfony, to add calendar event export for Gmail, iCloud, Outlook, and more.

## Features

- Generate RFC 5545 compliant .ics files for events and meetings
- Easy integration with Laravel and Symfony
- 100% test coverage with Pest
- Modern PHP (8.3, 8.4)

## Installation

```bash
composer require rumenx/php-calendar
```

## Usage

### Plain PHP

```php
use Rumenx\Calendar;

$calendar = new Calendar();
$calendar->addEvent([
    'summary' => 'Board Meeting',
    'start' => '2025-06-10 10:00:00',
    'end' => '2025-06-10 11:00:00',
    'location' => 'HQ',
    'description' => 'Discuss Q2 results.'
]);
file_put_contents('meeting.ics', $calendar->toIcs());
```

### Laravel Integration

Register the service provider in your `config/app.php` (if not auto-discovered):

```php
'providers' => [
    // ...
    Rumenx\Laravel\CalendarServiceProvider::class,
],
```

Then, use dependency injection or the service container:

```php
use Rumenx\Calendar;

public function download(Calendar $calendar)
{
    $calendar->addEvent([
        'summary' => 'Laravel Event',
        'start' => '2025-07-01 09:00:00',
        'end' => '2025-07-01 10:00:00',
    ]);
    return response($calendar->toIcs())
        ->header('Content-Type', 'text/calendar')
        ->header('Content-Disposition', 'attachment; filename="event.ics"');
}
```

### Symfony Integration

Register the bundle in your `config/bundles.php`:

```php
return [
    // ...
    Rumenx\Symfony\CalendarBundle::class => ['all' => true],
];
```

Then, use the service in a controller:

```php
use Rumenx\Calendar;
use Symfony\Component\HttpFoundation\Response;

public function download(Calendar $calendar): Response
{
    $calendar->addEvent([
        'summary' => 'Symfony Event',
        'start' => '2025-07-02 14:00:00',
        'end' => '2025-07-02 15:00:00',
    ]);
    return new Response(
        $calendar->toIcs(),
        200,
        [
            'Content-Type' => 'text/calendar',
            'Content-Disposition' => 'attachment; filename="event.ics"',
        ]
    );
}
```

### Advanced Usage

#### Recurring Events

```php
$calendar->addEvent([
    'summary' => 'Weekly Team Sync',
    'start' => '2025-06-12 10:00:00',
    'end' => '2025-06-12 11:00:00',
    'rrule' => 'FREQ=WEEKLY;BYDAY=TH;COUNT=10', // RFC 5545 recurrence rule
]);
```

#### Event with Attendees

```php
$calendar->addEvent([
    'summary' => 'Project Kickoff',
    'start' => '2025-06-15 09:00:00',
    'end' => '2025-06-15 10:30:00',
    'attendees' => [
        'mailto:alice@example.com',
        'mailto:bob@example.com',
    ],
]);
```

#### Event with Alarm/Reminder

```php
$calendar->addEvent([
    'summary' => 'Doctor Appointment',
    'start' => '2025-06-20 15:00:00',
    'end' => '2025-06-20 15:30:00',
    'alarm' => [
        'trigger' => '-PT15M', // 15 minutes before
        'description' => 'Appointment Reminder',
    ],
]);
```

## Testing

```bash
./vendor/bin/pest --coverage
```

## License

This project is licensed under the [MIT License](LICENSE.md).
