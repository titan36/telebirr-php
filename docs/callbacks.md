# Webhook Callbacks & Notifications

When a payment is completed successfully, Telebirr makes a server-to-server POST request to your `notify_url` with details of the payment. 

To process payments securely, you must verify the signature of the incoming request before fulfilling the user's order.

---

## 1. Defining the Callback Route

Define a route in your `routes/api.php` file:

```php
use App\Http\Controllers\PaymentController;

// Disable CSRF verification for this endpoint
Route::post('/telebirr/callback', [PaymentController::class, 'handleCallback']);
```

---

## 2. Implementing the Callback Controller

Create a controller to handle the incoming request, verify its signature, and trigger internal processes:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Ttechnos\Telebirr\Facades\Telebirr;
use Illuminate\Support\Facades\Log;
use App\Models\Order;

class PaymentController extends Controller
{
    public function handleCallback(Request $request)
    {
        $data = $request->all();

        // 1. Verify that the request actually came from Telebirr and hasn't been spoofed
        if (!Telebirr::verifySignature($data)) {
            Log::warning('Telebirr Callback: Signature verification failed!', $data);
            return response()->json(['msg' => 'Invalid signature'], 400);
        }

        // 2. Extract transaction details
        $merchOrderId = $data['merch_order_id'] ?? null;
        $tradeStatus = $data['trade_status'] ?? null;
        $totalAmount = $data['total_amount'] ?? null;
        $tradeNo = $data['trade_no'] ?? null; // Telebirr's transaction ID

        Log::info("Telebirr Callback verified: Order {$merchOrderId}, Status: {$tradeStatus}");

        if ($tradeStatus === 'PAY_SUCCESS') {
            // Find and process your order idempotently
            $order = Order::where('order_id', $merchOrderId)->first();

            if ($order && $order->status !== 'completed') {
                $order->update([
                    'status' => 'completed',
                    'telebirr_trade_no' => $tradeNo,
                    'paid_amount' => $totalAmount,
                ]);

                // Trigger email/notifications to user
            }
        }

        // 3. Respond with a JSON success payload so Telebirr knows we received the callback
        return response()->json([
            'code' => 0,
            'msg' => 'success'
        ]);
    }
}
```

---

## 3. Callback Payload Structure

The array received from Telebirr and verified by `verifySignature` contains the following fields:

| Field Name | Type | Description |
|------------|------|-------------|
| `merch_order_id` | `string` | The unique ID of the order on your system. |
| `trade_status` | `string` | The status of the trade (e.g., `PAY_SUCCESS`). |
| `total_amount` | `string` | The total payment amount (e.g., `100.50`). |
| `trade_no` | `string` | Telebirr's unique transaction number. |
| `trans_end_time` | `string` | Epoch timestamp of transaction completion. |
| `payment_order_id`| `string` | The internal Telebirr payment order identifier. |

---

## 4. Security Best Practices

### CSRF Exemption
Ensure you add your webhook route path to the `$except` array in `app/Http/Middleware/VerifyCsrfToken.php` so Laravel doesn't block the callback request:

```php
protected $except = [
    'api/telebirr/callback',
];
```

### Idempotency
Telebirr might send the callback notification multiple times if your server doesn't respond fast enough or if there are network issues. Always check if the order status is already marked as `completed` before updating or fulfilling it.
