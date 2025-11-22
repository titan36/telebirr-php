<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Telebirr Base URL
    |--------------------------------------------------------------------------
    |
    | The base URL for Telebirr API. Use the sandbox URL for testing
    | and production URL for live transactions.
    |
    */
    'base_url' => env('TELEBIRR_BASE_URL', 'https://developerportal.ethiotelebirr.et:38443/apiaccess/payment/gateway'),

    /*
    |--------------------------------------------------------------------------
    | Fabric App ID
    |--------------------------------------------------------------------------
    |
    | Your Telebirr Fabric Application ID
    |
    */
    'fabric_app_id' => env('TELEBIRR_FABRIC_APP_ID', ''),

    /*
    |--------------------------------------------------------------------------
    | App Secret
    |--------------------------------------------------------------------------
    |
    | Your Telebirr Application Secret
    |
    */
    'app_secret' => env('TELEBIRR_APP_SECRET', ''),

    /*
    |--------------------------------------------------------------------------
    | Merchant App ID
    |--------------------------------------------------------------------------
    |
    | Your Telebirr Merchant Application ID
    |
    */
    'merchant_app_id' => env('TELEBIRR_MERCHANT_APP_ID', ''),

    /*
    |--------------------------------------------------------------------------
    | Merchant Code
    |--------------------------------------------------------------------------
    |
    | Your Telebirr Merchant Code
    |
    */
    'merchant_code' => env('TELEBIRR_MERCHANT_CODE', ''),

    /*
    |--------------------------------------------------------------------------
    | Private Key Path
    |--------------------------------------------------------------------------
    |
    | Path to your RSA private key file
    |
    */
    'private_key_path' => env('TELEBIRR_PRIVATE_KEY_PATH', storage_path('app/telebirr/keys/private_key.pem')),

    /*
    |--------------------------------------------------------------------------
    | Public Key Path
    |--------------------------------------------------------------------------
    |
    | Path to your RSA public key file
    |
    */
    'public_key_path' => env('TELEBIRR_PUBLIC_KEY_PATH', storage_path('app/telebirr/keys/public_key.pem')),

    /*
    |--------------------------------------------------------------------------
    | Notification URL
    |--------------------------------------------------------------------------
    |
    | The URL where Telebirr will send payment notifications
    |
    */
    'notify_url' => env('TELEBIRR_NOTIFY_URL', null), // If null, will use route('telebirr.callback')

    /*
    |--------------------------------------------------------------------------
    | Transaction Currency
    |--------------------------------------------------------------------------
    |
    | Default currency for transactions
    |
    */
    'currency' => env('TELEBIRR_CURRENCY', 'ETB'),

    /*
    |--------------------------------------------------------------------------
    | Timeout
    |--------------------------------------------------------------------------
    |
    | Transaction timeout in minutes
    |
    */
    'timeout' => env('TELEBIRR_TIMEOUT', '120m'),

    /*
    |--------------------------------------------------------------------------
    | SSL Verification
    |--------------------------------------------------------------------------
    |
    | Enable/disable SSL verification (set to false only in development)
    |
    */
    'ssl_verify' => env('TELEBIRR_SSL_VERIFY', true),
];