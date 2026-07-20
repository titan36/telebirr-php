# Installation

## Requirements

- PHP 7.4 or higher
- Laravel 8.x, 9.x, 10.x, or 11.x
- OpenSSL extension
- ext-curl extension

## 1. Install via Composer

```bash
composer require ttechnos/telebirr
```

## 2. Laravel Setup

The package supports auto-discovery and will register its service provider and facade automatically.

If auto-discovery is disabled or you are using older Laravel versions, register them manually in `config/app.php`:

```php
'providers' => [
    Ttechnos\Telebirr\TelebirrServiceProvider::class,
],

'aliases' => [
    'Telebirr' => Ttechnos\Telebirr\Facades\Telebirr::class,
],
```

Next, read the [Configuration Guide](configuration.md) to set up credentials.
