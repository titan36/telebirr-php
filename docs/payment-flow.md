# Payment Flow (H5 Checkout)

The Telebirr H5 Checkout payment integration involves requesting a pre-order, getting a `prepay_id`, building a checkout redirect URL, and sending the user to pay.

Here is the complete implementation workflow.

---

## 1. Getting the Fabric Token (Automatically Handled)

Telebirr requires a Fabric auth token (`Bearer xxx`) before accessing any API endpoint. 
The package automatically manages this token internally under the hood when you request orders, query statuses, or initiate refunds.

If you need to fetch it manually for debugging or custom operations:

```php
use Ttechnos\Telebirr\Facades\Telebirr;

// Returns raw JSON response containing the Bearer token
$tokenResponse = Telebirr::getFabricToken();
```

---

## 2. Initiating a Pre-Order (Create Payment)

To prompt a user for payment, request a pre-order. This communicates your order information to Telebirr and generates a `prepay_id`.

```php
use Ttechnos\Telebirr\Facades\Telebirr;
use Ttechnos\Telebirr\Exceptions\TelebirrException;

try {
    $title = 'Premium Subscription Plan';
    $amount = 299.99; // Amount in ETB

    // Optional parameters (overrides configs if passed)
    $options = [
        'notify_url' => 'https://yourapp.com/api/telebirr/callback',
        'redirect_url' => 'https://yourapp.com/payment/success',
    ];

    $response = Telebirr::createOrder($title, $amount, $options);
    
    // The response contains the prepay_id and raw_request query
    $prepayId = $response['prepay_id'];
    $rawRequest = $response['raw_request'];

} catch (TelebirrException $e) {
    // Handle error (e.g., connection issue or validation failure)
    Log::error('Telebirr pre-order failed: ' . $e->getMessage());
}
```

---

## 3. Redirecting the User

Using the `raw_request` query parameters returned from `createOrder`, build the redirect URL to send the user to the secure Telebirr checkout screen:

```php
// Build redirect URL
$webBaseUrl = config('telebirr.web_base_url');
$checkoutUrl = $webBaseUrl . $rawRequest . '&version=1.0&trade_type=Checkout';

// In your controller:
return redirect()->away($checkoutUrl);
```

Once payment is completed, the user will be redirected back to your `redirect_url` configured in your `.env` or options.

---

## 4. Next Steps
To process the payment callback successfully and complete the order, read the [Webhook Callbacks Guide](callbacks.md).
