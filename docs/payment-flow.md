# Payment Flow (H5 Checkout)

The Telebirr checkout flow involves getting a `prepay_id` from a pre-order request, building the checkout URL, and redirecting the user to Telebirr.

## 1. Get Fabric Token (Optional)

The library automatically fetches and handles the Fabric Token under the hood during pre-orders, status queries, and refunds. You do not need to call this manually unless you are implementing a custom endpoint.

```php
use Ttechnos\Telebirr\Facades\Telebirr;

$tokenResponse = Telebirr::getFabricToken();
```

## 2. Create Pre-Order

Request a pre-order from Telebirr to obtain the `prepay_id` and the `raw_request` query string.

```php
use Ttechnos\Telebirr\Facades\Telebirr;
use Ttechnos\Telebirr\Exceptions\TelebirrException;

try {
    $response = Telebirr::createOrder('Product Description', 100.50, [
        'notify_url'   => 'https://yourapp.com/api/telebirr/callback',
        'redirect_url' => 'https://yourapp.com/payment/success',
    ]);
    
    $prepayId   = $response['prepay_id'];
    $rawRequest = $response['raw_request'];
} catch (TelebirrException $e) {
    Log::error('Telebirr pre-order error: ' . $e->getMessage());
}
```

## 3. Build Redirect URL

Use the returned `raw_request` to build the checkout redirection URL.

```php
$webBaseUrl  = config('telebirr.web_base_url');
$checkoutUrl = $webBaseUrl . $rawRequest . '&version=1.0&trade_type=Checkout';

return redirect()->away($checkoutUrl);
```

Next, read the [Webhook Callbacks Guide](callbacks.md).
