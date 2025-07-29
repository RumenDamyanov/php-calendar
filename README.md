# php-calendar

[![CI](https://github.com/RumenDamyanov/php-calendar/actions/workflows/ci.yml/badge.svg?branch=master)](https://github.com/RumenDamyanov/php-calendar/actions/workflows/ci.yml)
[![codecov](https://codecov.io/gh/RumenDamyanov/php-calendar/branch/master/graph/badge.svg)](https://codecov.io/gh/RumenDamyanov/php-calendar)

A simple, framework-agnostic PHP package for generating RFC 5545-compliant .ics calendar files. Built with the philosophy that calendar generation should be straightforward, reliable, and work everywhere.

## Philosophy

This package follows the Unix philosophy: **do one thing and do it well**. It generates standards-compliant .ics files without unnecessary dependencies, framework coupling, or complexity. Whether you're building a Laravel application, a Symfony project, or plain PHP script, this package just works.

Key principles:

- **Zero dependencies** - Works out of the box
- **Framework-agnostic** - No Laravel/Symfony lock-in
- **Standards-compliant** - Follows RFC 5545 specification
- **Simple API** - Intuitive methods that make sense
- **Modern PHP** - Built for PHP 8.3+ with strict types

## Installation

```bash
composer require rumenx/php-calendar
```

## Quick Start

```php
use Rumenx\Calendar;

// Create a calendar
$calendar = new Calendar();

// Add an event
$calendar->addEvent([
    'summary' => 'Team Meeting',
    'start' => '2025-08-15 10:00:00',
    'end' => '2025-08-15 11:00:00',
    'location' => 'Conference Room',
    'description' => 'Weekly team sync meeting'
]);

// Export as .ics file
$icsContent = $calendar->toIcs();

// Save to file or send as download
file_put_contents('meeting.ics', $icsContent);
```

## Real-World Examples

### Event Registration System

```php
// Generate calendar file for event registration confirmation
$calendar = new Calendar();
$calendar->addEvent([
    'summary' => 'PHP Conference 2025',
    'start' => '2025-09-20 09:00:00',
    'end' => '2025-09-20 17:00:00',
    'location' => 'Convention Center, Berlin',
    'description' => 'Annual PHP developers conference with talks and workshops.',
    'url' => 'https://phpconf2025.example.com'
]);

// Send as email attachment or download link
return response($calendar->toIcs())
    ->header('Content-Type', 'text/calendar')
    ->header('Content-Disposition', 'attachment; filename="phpconf2025.ics"');
```

### Appointment Booking

```php
// Doctor appointment with reminder
$calendar = new Calendar();
$calendar->addEvent([
    'summary' => 'Doctor Appointment',
    'start' => '2025-08-25 14:30:00',
    'end' => '2025-08-25 15:00:00',
    'location' => 'Medical Center, Room 205',
    'description' => 'Annual checkup with Dr. Smith',
    'alarm' => [
        'trigger' => '-PT30M', // 30 minutes before
        'description' => 'Appointment reminder'
    ]
]);
```

### Recurring Team Meetings

```php
// Weekly standup meeting
$calendar = new Calendar();
$calendar->addEvent([
    'summary' => 'Daily Standup',
    'start' => '2025-08-01 09:00:00',
    'end' => '2025-08-01 09:15:00',
    'location' => 'Zoom Meeting Room',
    'description' => 'Daily team standup meeting',
    'rrule' => 'FREQ=DAILY;BYDAY=MO,TU,WE,TH,FR;COUNT=50' // Weekdays only
]);
```

### Multi-Attendee Events

```php
// Project kickoff with team
$calendar = new Calendar();
$calendar->addEvent([
    'summary' => 'Project Alpha Kickoff',
    'start' => '2025-08-10 13:00:00',
    'end' => '2025-08-10 14:30:00',
    'location' => 'Main Conference Room',
    'description' => 'Kickoff meeting for the new Alpha project',
    'attendees' => [
        'mailto:john@company.com',
        'mailto:sarah@company.com',
        'mailto:mike@company.com'
    ],
]);
```

## Framework Integration

### Laravel

```php
// In a controller
public function downloadEvent()
{
    $calendar = new Calendar();
    $calendar->addEvent([
        'summary' => 'Laravel Meetup',
        'start' => '2025-09-15 18:00:00',
        'end' => '2025-09-15 20:00:00',
    ]);

    return response($calendar->toIcs())
        ->header('Content-Type', 'text/calendar')
        ->header('Content-Disposition', 'attachment; filename="meetup.ics"');
}
```

### Symfony

```php
// In a controller
public function downloadEvent(): Response
{
    $calendar = new Calendar();
    $calendar->addEvent([
        'summary' => 'Symfony Event',
        'start' => '2025-09-15 18:00:00',
        'end' => '2025-09-15 20:00:00',
    ]);

    return new Response(
        $calendar->toIcs(),
        200,
        [
            'Content-Type' => 'text/calendar',
            'Content-Disposition' => 'attachment; filename="event.ics"'
        ]
    );
}
```

## Supported Event Properties

- `summary` (required) - Event title/subject
- `start` (required) - Start date/time (YYYY-MM-DD HH:MM:SS)
- `end` (required) - End date/time (YYYY-MM-DD HH:MM:SS)
- `location` - Event location
- `description` - Event description
- `rrule` - Recurrence rule (RFC 5545 format)
- `attendees` - Array of email addresses
- `organizer` - Organizer email address
- `alarm` - Alarm/reminder settings
- `url` - Event URL

## Development

```bash
# Install dependencies
composer install

# Run tests
composer test

# Run static analysis
composer analyze

# Check code style
composer style

# Fix code style
composer style-fix

# Run all quality checks
composer quality
```

## Contributing

Contributions are welcome! This package follows PSR-12 coding standards and requires 95%+ test coverage. Please ensure your contributions include appropriate tests.

Please see our [Contributing Guide](CONTRIBUTING.md) for details on how to get started.

## Security

If you discover any security-related issues, please review our [Security Policy](SECURITY.md) for information on how to report them responsibly.

## Changelog

All notable changes are documented in our [Changelog](CHANGELOG.md).

## Support the Project

If you find this project useful, please consider [supporting its development](FUNDING.md).

## License

This project is licensed under the [MIT License](LICENSE.md).
