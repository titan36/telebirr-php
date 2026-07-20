# Configuration

## 1. Environment Variables (.env)

Add the following configuration to your `.env` file:

```env
TELEBIRR_BASE_URL=https://developerportal.ethiotelebirr.et:38443/apiaccess/payment/gateway
TELEBIRR_WEB_BASE_URL=https://developerportal.ethiotelebirr.et:38443/payment/web/paygate?

# Production URLs:
# TELEBIRR_BASE_URL=https://api.ethiotelecom.et/apiaccess/payment/gateway
# TELEBIRR_WEB_BASE_URL=https://api.ethiotelecom.et/payment/web/paygate?

# RSA Key Paths
TELEBIRR_PRIVATE_KEY_PATH=storage/app/telebirr/keys/private_key.pem
TELEBIRR_PUBLIC_KEY_PATH=storage/app/telebirr/keys/public_key.pem

# Merchant Portal Credentials
TELEBIRR_FABRIC_APP_ID=
TELEBIRR_APP_SECRET=
TELEBIRR_MERCHANT_APP_ID=
TELEBIRR_MERCHANT_CODE=

# Callbacks
TELEBIRR_NOTIFY_URL=https://yourapp.com/api/telebirr/callback
TELEBIRR_REDIRECT_URL=https://yourapp.com/payment/success

# SSL Verification (Set to false only in development)
TELEBIRR_SSL_VERIFY=true
```

## 2. Publish Configuration & Key Placeholders

Publish configuration and placeholder key files to your Laravel project structure:

```bash
# Publish config/telebirr.php
php artisan vendor:publish --tag=telebirr-config

# Publish placeholder keys to storage/app/telebirr/keys/
php artisan vendor:publish --tag=telebirr-keys
```

## 3. Key Pair Configuration

Telebirr requires RSA key pairs for signing and verifying payload data:
1. `private_key.pem`: Your private key (used to sign requests).
2. `public_key.pem`: Telebirr's public key (obtained from developer portal, used to verify callbacks).

### Generating Test Key Pairs

To generate RSA key pairs using the OpenSSL CLI tool:

```bash
# Generate private key
openssl genrsa -out private_key.pem 2048

# Extract public key
openssl rsa -in private_key.pem -pubout -out public_key.pem
```

Place these files in `storage/app/telebirr/keys/` as specified in your `.env`.

> **Note:** Do not commit `private_key.pem` to version control. Add it to your `.gitignore`.

Next, read the [Payment Flow Guide](payment-flow.md).
