# Telebirr Laravel Package

[![Latest Version on Packagist](https://img.shields.io/packagist/v/ttechnos/telebirr.svg?style=flat-square)](https://packagist.org/packages/ttechnos/telebirr)
[![License: MIT](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)](LICENSE)
[![PHP Version](https://img.shields.io/badge/php-%3E%3D7.4-8892BF.svg?style=flat-square)](https://php.net)

A Laravel package for integrating the Telebirr H5 (Web Checkout) payment gateway. Provides methods to create orders, handle auth tokens, and verify callback signatures.

---

## Documentation

Browse the step-by-step guides in the `docs` folder:
- [Installation Guide](docs/installation.md)
- [Configuration Guide](docs/configuration.md)
- [Payment Flow](docs/payment-flow.md)
- [Webhook Callbacks](docs/callbacks.md)
- [Queries & Refunds](docs/queries-refunds.md)

---

## Requirements

- PHP 7.4 or higher
- Laravel 8.x, 9.x, 10.x, 11.x
- OpenSSL extension

## Installation

```bash
composer require ttechnos/telebirr
```

## Quick Start

```php
use Ttechnos\Telebirr\Facades\Telebirr;

// Create payment order
$order = Telebirr::createOrder('Product Title', 100.50, [
    'notify_url'   => 'https://yourapp.com/api/telebirr/callback',
    'redirect_url' => 'https://yourapp.com/payment/success',
]);

$prepayId   = $order['prepay_id'];
$rawRequest = $order['raw_request'];

// Query order status
$status = Telebirr::queryOrder($prepayId);

// Refund a payment
$refund = Telebirr::refundOrder(10.00, $paymentOrderId);

// Verify webhook signature
$isValid = Telebirr::verifySignature($requestData);
```

## Features

- H5 Web Checkout (B2C)
- Fabric Token Auth
- Query Order Status
- Refund Payments
- RSA-PSS Webhook Signature Verification

## Contributing

See [CONTRIBUTING.md](CONTRIBUTING.md) for details on how to contribute.

## License

MIT License. See [LICENSE](LICENSE) for more information.