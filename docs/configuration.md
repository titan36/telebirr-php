# Configuration Guide

Properly configuring credentials and cryptographic key pairs is crucial to integrating with Telebirr securely.

## 1. Environment Variables (.env)

Add the following variables to your Laravel project's `.env` file and fill in your merchant details from the Ethio Telecom developer portal:

```env
# Telebirr API Endpoints
# Sandbox URL (Testing):
TELEBIRR_BASE_URL=https://developerportal.ethiotelebirr.et:38443/apiaccess/payment/gateway
TELEBIRR_WEB_BASE_URL=https://developerportal.ethiotelebirr.et:38443/payment/web/paygate?

# Production URL (Uncomment for Live environment):
# TELEBIRR_BASE_URL=https://api.ethiotelecom.et/apiaccess/payment/gateway
# TELEBIRR_WEB_BASE_URL=https://api.ethiotelecom.et/payment/web/paygate?

# Key Paths
TELEBIRR_PRIVATE_KEY_PATH=storage/app/telebirr/keys/private_key.pem
TELEBIRR_PUBLIC_KEY_PATH=storage/app/telebirr/keys/public_key.pem

# Merchant Credentials
TELEBIRR_FABRIC_APP_ID=your_fabric_app_id
TELEBIRR_APP_SECRET=your_app_secret
TELEBIRR_MERCHANT_APP_ID=your_merchant_app_id
TELEBIRR_MERCHANT_CODE=your_merchant_code

# Webhook URLs
TELEBIRR_NOTIFY_URL=https://yourapp.com/api/telebirr/callback
TELEBIRR_REDIRECT_URL=https://yourapp.com/payment/success

# SSL Verification (Set to false only in local development)
TELEBIRR_SSL_VERIFY=true
```

---

## 2. Publish Configuration & Key Placeholders

Run the artisan publish commands to generate files inside your Laravel directory structure:

```bash
# Publish configuration file to config/telebirr.php
php artisan vendor:publish --tag=telebirr-config

# Publish stub RSA key placeholders to storage/app/telebirr/keys/
php artisan vendor:publish --tag=telebirr-keys
```

---

## 3. RSA Key Pair Configuration

Telebirr uses asymmetric RSA keys to sign and verify request data. You need two files inside `storage/app/telebirr/keys/`:
1. `private_key.pem`: Your merchant private key used to sign requests sent to Telebirr.
2. `public_key.pem`: Telebirr's public key (obtained from the developer portal) used to verify webhook notifications.

### Generating Your Keys (For Sandbox / Testing)

You can generate a valid RSA key pair using the OpenSSL CLI tool:

```bash
# Generate private key
openssl genrsa -out private_key.pem 2048

# Extract public key (submit this to the Telebirr developer portal)
openssl rsa -in private_key.pem -pubout -out public_key.pem
```

Ensure `private_key.pem` is copied to your local storage directory:
`storage/app/telebirr/keys/private_key.pem`

> ⚠️ **Warning:** Keep your `private_key.pem` secure. **Never commit your private key** to version control. Add it to your `.gitignore` file.

---

## 4. Next Steps
Once configuration is complete, you are ready to initiate checkout requests. See the [Payment Flow Guide](payment-flow.md).
