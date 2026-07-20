# Installation Guide

Follow these steps to install the Telebirr payment integration package in your Laravel project.

## Requirements

- PHP `7.4` or higher
- Laravel `8.x`, `9.x`, `10.x`, or `11.x`
- OpenSSL PHP extension (`ext-openssl` or `phpseclib/phpseclib` which is bundled)
- `ext-curl` enabled in php.ini

---

## 1. Install via Composer

Add the package to your project using Composer:

```bash
composer require ttechnos/telebirr
```

---

## 2. Service Discovery (Auto-Discovery)

This package supports Laravel Package Auto-Discovery. It will automatically register the following:
* Service Provider: `Ttechnos\Telebirr\TelebirrServiceProvider`
* Facade: `Ttechnos\Telebirr\Facades\Telebirr`

### Manual Registration (Laravel 8/9 or older setups)

If you have auto-discovery disabled or are on older Laravel versions, add these manually to your `config/app.php`:

```php
'providers' => [
    // ... other providers
    Ttechnos\Telebirr\TelebirrServiceProvider::class,
],

'aliases' => [
    // ... other aliases
    'Telebirr' => Ttechnos\Telebirr\Facades\Telebirr::class,
],
```

---

## 3. Next Steps
Once installed, proceed to configure your keys and credentials in the [Configuration Guide](configuration.md).
