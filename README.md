# Telebirr Laravel Package

[![Latest Version on Packagist](https://img.shields.io/packagist/v/ttechnos/telebirr.svg?style=flat-square)](https://packagist.org/packages/ttechnos/telebirr)
[![License: MIT](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)](LICENSE)
[![PHP Version](https://img.shields.io/badge/php-%3E%3D7.4-8892BF.svg?style=flat-square)](https://php.net)

A Laravel package for integrating the **Telebirr H5 (Web Checkout)** payment gateway. Provides a clean, expressive API to create payment orders, obtain Fabric tokens, and verify signatures.

---

## Requirements

- PHP 7.4 or higher
- Laravel 8.x, 9.x, 10.x, 11.x
- OpenSSL PHP extension
- Telebirr merchant account with API credentials

## Installation

Install via Composer:

```bash
composer require ttechnos/telebirr
```

The package will auto-register its service provider and facade.

## Configuration

### 1. Add environment variables

Add the following to your `.env` file:

```env
TELEBIRR_PRIVATE_KEY_PATH=storage/app/telebirr/keys/private_key.pem
TELEBIRR_PUBLIC_KEY_PATH=storage/app/telebirr/keys/public_key.pem
TELEBIRR_WEB_BASE_URL=https://developerportal.ethiotelebirr.et:38443/payment/web/paygate?
TELEBIRR_BASE_URL=https://developerportal.ethiotelebirr.et:38443/apiaccess/payment/gateway
TELEBIRR_FABRIC_APP_ID=your_fabric_app_id
TELEBIRR_APP_SECRET=your_app_secret
TELEBIRR_MERCHANT_APP_ID=your_merchant_app_id
TELEBIRR_MERCHANT_CODE=your_merchant_code
TELEBIRR_SSL_VERIFY=false
```

### 2. Set up your keys

Place your RSA key files in the path specified in `.env`:

```
storage/app/telebirr/keys/private_key.pem
storage/app/telebirr/keys/public_key.pem
```
### 3. Publish config & example keys (optional)

```bash
php artisan vendor:publish --tag=telebirr-config
php artisan vendor:publish --tag=telebirr-keys
```

## Usage

### Using the Facade

```php
use Ttechnos\Telebirr\Facades\Telebirr;

// Get Fabric token
$token = Telebirr::getFabricToken();

// Create a payment order
$order = Telebirr::createOrder('Test Product', 100.50, [
    'notify_url'   => 'https://yourapp.com/api/telebirr/callback',
    'redirect_url' => 'https://yourapp.com/payment/success',
]);

// Query order status (check if paid)
$status = Telebirr::queryOrder($prepayId); // Or queryOrder(null, $merchantOrderId)
$tradeStatus = $status['biz_content']['trade_status'] ?? ''; // 'PAY_SUCCESS'

// Refund a payment
$refund = Telebirr::refundOrder(10.00, $paymentOrderId); // Or refundOrder(10.00, null, $merchantOrderId)

// Verify webhook/callback signature
$isValid = Telebirr::verifySignature($requestData);
```

### Using the Service directly

```php
$telebirr = app('telebirr');

$token   = $telebirr->getFabricToken();
$order   = $telebirr->createOrder('Test Product', 100.50);
$status  = $telebirr->queryOrder($prepayId);
$refund  = $telebirr->refundOrder(10.00, $paymentOrderId);
$isValid = $telebirr->verifySignature($data);
```

## Supported Features

| Feature | Status |
|---------|--------|
| H5 Web Checkout (B2C) | ✅ Fully Supported |
| Fabric Token Auth | ✅ Fully Supported |
| Query Order Status | ✅ Fully Supported |
| Refund Payments | ✅ Fully Supported |
| RSA-PSS Signature Verification | ✅ Fully Supported |
| In-App SDK Payment | 🔜 Coming soon |
| C2B (Scan to Pay) | 🔜 Coming soon |
| B2B Payment | 🔜 Coming soon |

## Contributing

Contributions are welcome! Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

We especially need help with:
- In-App SDK payment integration
- C2B and B2B payment support
- Unit and integration tests
- Documentation improvements

## Changelog

Please see [CHANGELOG.md](CHANGELOG.md) for a list of recent changes.

## License

The MIT License (MIT). Please see [LICENSE](LICENSE) for more information.

---

**Built with ❤️ for the Ethiopian developer community by [TTechnos](https://github.com/titan36)**