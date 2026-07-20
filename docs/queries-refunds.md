# Order Queries & Refunds

For complete transaction control, you can query the status of orders at any time or issue refunds to users.

---

## 1. Querying Order Status

Use the `queryOrder` endpoint to check the transaction status on Telebirr's servers. This is highly useful for:
- Manual transaction reconciliation
- Handling payment confirmation when webhooks fail
- Polling order completion status

You can query an order using either the `prepay_id` or your system's `merch_order_id` (at least one is required).

```php
use Ttechnos\Telebirr\Facades\Telebirr;
use Ttechnos\Telebirr\Exceptions\TelebirrException;

try {
    // Option A: Query using prepay_id
    $status = Telebirr::queryOrder($prepayId);

    // Option B: Query using merchant order ID
    $status = Telebirr::queryOrder(null, $merchantOrderId);

    // Parse status
    $code = $status['code'] ?? null; // '200' represents a successful API communication
    $tradeStatus = $status['biz_content']['trade_status'] ?? ''; // 'PAY_SUCCESS' or 'PAY_FAIL'
    
    if ($tradeStatus === 'PAY_SUCCESS') {
        // Order is verified as paid
    }

} catch (TelebirrException $e) {
    Log::error('Query order failed: ' . $e->getMessage());
}
```

---

## 2. Refunding Payments

You can refund a transaction programmatically (full or partial refunds). 

To request a refund, you must specify the refund amount and identify the transaction using either the `payment_order_id` (obtained from Telebirr's callback payload) or your system's `merch_order_id`.

```php
use Ttechnos\Telebirr\Facades\Telebirr;
use Ttechnos\Telebirr\Exceptions\TelebirrException;

try {
    $refundAmount = 100.50; // Amount in ETB to refund (can be partial)
    $paymentOrderId = '123456789'; // Telebirr's payment order ID

    // Optional parameters
    $reason = 'Item out of stock';
    $refundOrderId = 'ref_' . time(); // Optional custom unique refund request ID

    // Process refund using Telebirr payment order ID
    $response = Telebirr::refundOrder($refundAmount, $paymentOrderId, null, $reason, $refundOrderId);

    // Alternatively, process refund using your system's merchant order ID
    // $response = Telebirr::refundOrder($refundAmount, null, $merchantOrderId, $reason);

    $code = $response['code'] ?? null;
    $refundStatus = $response['biz_content']['refund_status'] ?? ''; // 'REFUND_SUCCESS'

    if ($refundStatus === 'REFUND_SUCCESS') {
        Log::info('Payment refunded successfully');
    }

} catch (TelebirrException $e) {
    Log::error('Refund failed: ' . $e->getMessage());
}
```
