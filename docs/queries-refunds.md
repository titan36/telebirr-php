# Queries & Refunds

## 1. Querying Order Status

Query transaction status directly on Telebirr's servers using either the `prepay_id` or your system's `merch_order_id`.

```php
use Ttechnos\Telebirr\Facades\Telebirr;
use Ttechnos\Telebirr\Exceptions\TelebirrException;

try {
    // Option A: Query using prepay_id
    $status = Telebirr::queryOrder($prepayId);

    // Option B: Query using merchant order ID
    // $status = Telebirr::queryOrder(null, $merchantOrderId);

    $tradeStatus = $status['biz_content']['trade_status'] ?? ''; // 'PAY_SUCCESS' or 'PAY_FAIL'
    
    if ($tradeStatus === 'PAY_SUCCESS') {
        // Order paid successfully
    }
} catch (TelebirrException $e) {
    Log::error('Query order status error: ' . $e->getMessage());
}
```

## 2. Refunding Payments

Initiate full or partial refunds using the refund amount and either the Telebirr `payment_order_id` or your system's `merch_order_id`.

```php
use Ttechnos\Telebirr\Facades\Telebirr;
use Ttechnos\Telebirr\Exceptions\TelebirrException;

try {
    $refundAmount   = 100.50;
    $paymentOrderId = '123456789'; // Telebirr payment order ID
    $reason         = 'Item out of stock';
    $refundOrderId  = 'ref_' . time(); // Optional unique refund request ID

    // Refund using Telebirr payment order ID
    $response = Telebirr::refundOrder($refundAmount, $paymentOrderId, null, $reason, $refundOrderId);

    // Refund using system's merchant order ID
    // $response = Telebirr::refundOrder($refundAmount, null, $merchantOrderId, $reason);

    $refundStatus = $response['biz_content']['refund_status'] ?? ''; // 'REFUND_SUCCESS'

    if ($refundStatus === 'REFUND_SUCCESS') {
        Log::info('Transaction refunded successfully');
    }
} catch (TelebirrException $e) {
    Log::error('Refund error: ' . $e->getMessage());
}
```
