# Webhook Callbacks

When a payment is completed, Telebirr makes a server-to-server POST request to your `notify_url` with details of the transaction. You must verify the signature of the incoming request payload before fulfilling the order.

## 1. Register Callback Route

Define a route in `routes/api.php`:

```php
use App\Http\Controllers\PaymentController;

Route::post('/telebirr/callback', [PaymentController::class, 'handleCallback']);
```

### CSRF Exception

Add the callback path to the `$except` array in `app/Http/Middleware/VerifyCsrfToken.php`:

```php
protected $except = [
    'api/telebirr/callback',
];
```

## 2. Controller Example

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

        // Verify webhook signature
        if (!Telebirr::verifySignature($data)) {
            Log::warning('Telebirr Callback: Invalid signature', $data);
            return response()->json(['msg' => 'Invalid signature'], 400);
        }

        $merchOrderId = $data['merch_order_id'] ?? null;
        $tradeStatus  = $data['trade_status'] ?? null;
        $totalAmount  = $data['total_amount'] ?? null;
        $tradeNo      = $data['trade_no'] ?? null;

        if ($tradeStatus === 'PAY_SUCCESS') {
            // Process order status (ensure idempotency check)
            $order = Order::where('order_id', $merchOrderId)->first();

            if ($order && $order->status !== 'completed') {
                $order->update([
                    'status' => 'completed',
                    'telebirr_trade_no' => $tradeNo,
                    'paid_amount' => $totalAmount,
                ]);
            }
        }

        // Acknowledge receipt to Telebirr
        return response()->json([
            'code' => 0,
            'msg' => 'success'
        ]);
    }
}
```

## 3. Callback Parameter Reference

| Field | Type | Description |
|-------|------|-------------|
| `merch_order_id` | `string` | The unique ID of the order on your system |
| `trade_status` | `string` | Status of the trade (e.g., `PAY_SUCCESS`) |
| `total_amount` | `string` | Total payment amount (e.g., `100.50`) |
| `trade_no` | `string` | Telebirr's unique transaction identifier |
| `trans_end_time` | `string` | Epoch timestamp of transaction completion |
| `payment_order_id`| `string` | Telebirr payment order identifier |
