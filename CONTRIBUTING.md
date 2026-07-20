# Contributing to Telebirr Laravel Package

Thank you for considering contributing to the Telebirr Laravel package! 🎉

## How to Contribute

### Reporting Bugs

- Open an [issue](https://github.com/titan36/telebirr-php/issues) with a clear description
- Include steps to reproduce, expected vs actual behavior
- Add your Laravel version, PHP version, and package version

### Suggesting Features

- Open an issue with the `feature request` label
- Describe the use case and why it would be valuable
- If possible, include examples of how the API should look

### Submitting Pull Requests

1. **Fork** the repository
2. **Clone** your fork:
   ```bash
   git clone https://github.com/YOUR_USERNAME/telebirr-php.git
   cd telebirr-php
   composer install
   ```
3. **Create a branch** for your change:
   ```bash
   git checkout -b feature/your-feature-name
   ```
4. **Make your changes** and add tests if applicable
5. **Commit** with a clear message:
   ```bash
   git commit -m "feat: add support for C2B payments"
   ```
6. **Push** and open a Pull Request:
   ```bash
   git push origin feature/your-feature-name
   ```

### Commit Message Convention

We follow [Conventional Commits](https://www.conventionalcommits.org/):

- `feat:` — New feature
- `fix:` — Bug fix
- `docs:` — Documentation changes
- `refactor:` — Code refactoring
- `test:` — Adding or updating tests
- `chore:` — Maintenance tasks

## Areas Where Help is Needed

We'd love contributions in these areas:

- **In-App (SDK) payment** integration
- **C2B (scan-to-pay)** support
- **B2B payment** support
- **Webhook/callback** handling improvements
- **Unit and integration tests**
- **Documentation** improvements
- **Laravel 11+** compatibility testing

## Development Setup

```bash
# Install dependencies
composer install

# Run tests
vendor/bin/phpunit
```

## Code Style

- Follow PSR-12 coding standards
- Add PHPDoc blocks to all public methods
- Keep methods focused and single-purpose

## Questions?

If you have questions, feel free to open a [discussion](https://github.com/titan36/telebirr-php/issues) or reach out.

Thank you for helping make Telebirr integration easier for Ethiopian developers! 🇪🇹
