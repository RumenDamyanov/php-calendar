# Security Policy

## Supported Versions

We actively support the following versions with security updates:

| Version | PHP  | Supported          |
| ------- | ---- | ------------------ |
| 1.1.x   | 8.3+ | :white_check_mark: |
| 1.0.x   | 8.2+ | :white_check_mark: until PHP 8.2 EOL (31 Dec 2026) |

Runtime PHP must be a patched build. PHP2Shell ([CVE-2026-109618](https://www.cve.org/CVERecord?id=CVE-2026-109618), [CVE-2026-109619](https://www.cve.org/CVERecord?id=CVE-2026-109619)) is in the PHP engine, not in this library. Upgrade the interpreter to **8.5.12+**, **8.4.27+**, **8.3.36+**, or **8.2.35+**. There is no application-level workaround; see the [PHP Foundation advisory](https://thephp.foundation/blog/2026/10/21/todays-php-security-release-and-how-were-strengthening-php-security/) and [CISA alert](https://www.cisa.gov/news-events/alerts/2026/10/21/critical-vulnerabilities-php-require-immediate-patching).

## Reporting a Vulnerability

If you discover a security vulnerability in php-calendar, please report it to us privately by emailing [contact@rumenx.com](mailto:contact@rumenx.com).

**Please do not report security vulnerabilities through public GitHub issues.**

When reporting a vulnerability, please include:

- A description of the vulnerability
- Steps to reproduce the issue
- Affected versions
- Any potential mitigations you've identified

We will acknowledge receipt of your vulnerability report within 48 hours and provide a detailed response within 5 business days indicating the next steps in handling your report.

After the initial reply to your report, we will keep you informed of the progress being made towards a fix and may ask for additional information or guidance.

## Security Updates

Security updates will be released as soon as possible after a vulnerability is confirmed and a fix is available. We recommend keeping your installation up to date with the latest version.

## Responsible Disclosure

We appreciate the security research community and believe responsible disclosure is the best approach to keeping our users safe. We will acknowledge and credit researchers who report vulnerabilities responsibly.
