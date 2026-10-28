# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.1.0] - 2026-10-28

### Security

- Escape RFC 5545 text so a comma, semicolon, backslash, or newline cannot break out of its field. A carriage return is escaped as `\n` instead of being deleted.
- Reject a recurrence rule, address, or URL that contains a control character, so `RRULE` and `ATTENDEE` cannot inject another property.
- Quote calendar parameter values that contain `:`, `;`, `,`, or a space.
- Document that PHP2Shell ([CVE-2026-109618](https://www.cve.org/CVERecord?id=CVE-2026-109618), [CVE-2026-109619](https://www.cve.org/CVERecord?id=CVE-2026-109619)) is in the PHP engine: this package cannot patch it. Run PHP **8.3.36+**, **8.4.27+**, or **8.5.12+** ([PHP Foundation advisory](https://thephp.foundation/blog/2026/10/21/todays-php-security-release-and-how-were-strengthening-php-security/), [CISA alert](https://www.cisa.gov/news-events/alerts/2026/10/21/critical-vulnerabilities-php-require-immediate-patching)).

### Changed

- Require PHP **^8.3**. PHP 8.2 reaches upstream end of life on 31 December 2026; stay on 1.0.x if you must remain on 8.2 (and use **8.2.35+** until then).
- Reject an event whose end is before its start, and reject a date PHP cannot parse.
- Alarm triggers must be a duration or a UTC timestamp.
- Fold content lines at 75 octets without splitting a UTF-8 character.
- CI tests PHP 8.3, 8.4, and 8.5. Coverage is collected on 8.4 only. Dev tooling is Pest 4 and PHPUnit 12.

### Fixed

- Write the documented `url` and `organizer` properties.
- Prefix a bare email address with `mailto:`.

## [1.0.0] - 2025-07-29

### Added

- Initial release of php-calendar
- RFC 5545 compliant .ics file generation
- Support for basic event properties (summary, start, end, location, description)
- Support for advanced event features:
  - Recurring events with RRULE
  - Event attendees
  - Event alarms/reminders
- Laravel integration with CalendarServiceProvider
- Symfony integration with CalendarBundle
- Comprehensive test suite with 100% code coverage
- CI/CD workflow with PHP 8.3 and 8.4 support
- Codecov integration for coverage reporting

### Documentation

- Complete README with usage examples
- API documentation with detailed comments
- Framework integration guides

[Unreleased]: https://github.com/RumenDamyanov/php-calendar/compare/v1.0.0...HEAD
[1.0.0]: https://github.com/RumenDamyanov/php-calendar/releases/tag/v1.0.0
