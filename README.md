# Telebirr Laravel Package

A Laravel package to integrate **Telebirr payment gateway**. Provides easy methods to create orders, get Fabric tokens, and verify signatures.

## 1 Setup

### 1. Add environment variables

Add the following to your `.env` file:

```env
TELEBIRR_PRIVATE_KEY_PATH=storage/app/telebirr/keys/private_key.pem
TELEBIRR_PUBLIC_KEY_PATH=storage/app/telebirr/keys/public_key.pem
TELEBIRR_WEB_BASE_URL=https://developerportal.ethiotelebirr.et:38443/payment/web/paygate?
TELEBIRR_BASE_URL=https://developerportal.ethiotelebirr.et:38443/apiaccess/payment/gateway
TELEBIRR_FABRIC_APP_ID=
TELEBIRR_APP_SECRET=
TELEBIRR_MERCHANT_APP_ID=
TELEBIRR_MERCHANT_CODE=
TELEBIRR_SSL_VERIFY=false
```

> **Note:** Fill in your Fabric App ID, App Secret, Merchant App ID, and Merchant Code.

### 2. Copy the keys

Place your keys in the path specified in `.env`:

```
storage/app/telebirr/keys/private_key.pem
storage/app/telebirr/keys/public_key.pem
```

> **Do not commit the private key** to version control.

### 3. Publish config (optional)

```bash
php artisan vendor:publish --tag=telebirr-config
php artisan vendor:publish --tag=telebirr-keys
```

This will copy the config and keys into your Laravel project.

## 2 Installation

Install via Composer:

```bash
composer require ttechnos/telebirr:dev-main
```

## 3 Usage

### Using the Facade

```php
use Ttechnos\Telebirr\Facades\Telebirr;

// Get Fabric token
$token = Telebirr::getFabricToken();

// Create an order
$order = Telebirr::createOrder('Test Product', 100.50, [
    'description' => 'Payment for test product',
]);

```

### Using the Service directly

```php
$telebirr = app('telebirr');

$token = $telebirr->getFabricToken();
$order = $telebirr->createOrder('Test Product', 100.50);
$isValid = $telebirr->verifySignature($data);
```

## 5 License

MIT License.