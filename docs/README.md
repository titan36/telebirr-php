# Telebirr Laravel Package Documentation

Welcome to the documentation for the Telebirr payment integration package for Laravel. This documentation covers everything you need to know to integrate Telebirr H5 payments securely and professionally.

## Documentation Index

1. [**Installation**](installation.md)
   * System requirements
   * Installing via Composer
   * Laravel package discovery

2. [**Configuration**](configuration.md)
   * Environment variables (.env)
   * Configuration publishing
   * Key setup (RSA Private/Public Keys)

3. [**Payment Flow**](payment-flow.md)
   * Step 1: Getting Fabric token
   * Step 2: Creating a pre-order
   * Step 3: Redirecting users to Telebirr Checkout

4. [**Handling Webhook Callbacks**](callbacks.md)
   * Setting up the notification endpoint
   * Verifying signatures
   * Listening to payment events
   * Returning responses

5. [**Queries & Refunds**](queries-refunds.md)
   * Checking transaction status programmatically
   * Processing refunds (full or partial)

---

## Technical Support & Contributing

If you encounter issues or find bugs, feel free to:
- Review the [Contributing Guide](../CONTRIBUTING.md)
- Open an issue in the [GitHub Repository](https://github.com/titan36/telebirr-php)
