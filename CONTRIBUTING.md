# Contributing to php-calendar

Thank you for your interest in contributing to php-calendar! We welcome contributions from the community.

## Getting Started

1. Fork the repository on GitHub
2. Clone your fork locally
3. Create a new branch for your feature or bug fix
4. Make your changes
5. Test your changes
6. Submit a pull request

## Development Setup

### Prerequisites

- PHP 8.3+
- Composer

### Installation

```bash
git clone https://github.com/YourUsername/php-calendar.git
cd php-calendar
composer install
```

### Running Tests

```bash
# Run all tests
./vendor/bin/pest

# Run tests with coverage
./vendor/bin/pest --coverage

# Run specific test files
./vendor/bin/pest tests/CalendarTest.php
```

## Coding Standards

- Follow PSR-12 coding standards
- Use meaningful variable and method names
- Add PHPDoc comments for all public methods
- Keep methods focused and single-purpose

## Testing

- All new features must include tests
- Aim for 100% code coverage
- Use descriptive test names that explain what is being tested
- Test both success and failure scenarios

## Pull Request Guidelines

### Before Submitting

- Ensure all tests pass
- Update documentation if needed
- Add entries to CHANGELOG.md for new features or breaking changes
- Make sure your code follows the project's coding standards

### Pull Request Process

1. Create a clear and descriptive title
2. Provide a detailed description of what your PR does
3. Reference any related issues
4. Include screenshots or examples if applicable
5. Request review from maintainers

### Example PR Description

```markdown
## What this PR does

Brief description of the feature or fix.

## Related Issues

Closes #123

## Testing

- [ ] Added unit tests
- [ ] All tests pass
- [ ] Manual testing completed

## Documentation

- [ ] Updated README if needed
- [ ] Added/updated PHPDoc comments
- [ ] Updated CHANGELOG.md
```

## Reporting Issues

When reporting issues, please include:

- PHP version
- Composer version
- Steps to reproduce
- Expected behavior
- Actual behavior
- Error messages (if any)

## Code of Conduct

Please be respectful and constructive in all interactions. We want to maintain a welcoming environment for all contributors.

## Questions?

If you have questions about contributing, feel free to:

- Open an issue for discussion
- Contact the maintainers at [contact@rumenx.com](mailto:contact@rumenx.com)

Thank you for contributing! 🎉
