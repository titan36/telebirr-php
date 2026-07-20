<?php

namespace Ttechnos\Telebirr\Services;

use Ttechnos\Telebirr\Exceptions\TelebirrException;
use Ttechnos\Telebirr\Utils\SignatureHelper;

class RefundOrderService
{
    protected $baseUrl;
    protected $fabricAppId;
    protected $appSecret;
    protected $merchantAppId;
    protected $merchantCode;
    protected $privateKeyPath;

    public function __construct(
        $baseUrl,
        $fabricAppId,
        $appSecret,
        $merchantAppId,
        $merchantCode,
        $privateKeyPath
    ) {
        $this->baseUrl = $baseUrl;
        $this->fabricAppId = $fabricAppId;
        $this->appSecret = $appSecret;
        $this->merchantAppId = $merchantAppId;
        $this->merchantCode = $merchantCode;
        $this->privateKeyPath = $privateKeyPath;
    }

    /**
     * Refund order
     *
     * @param string|int|float $refundAmount Refund amount (ETB)
     * @param string|null $paymentOrderId Optional payment order ID from Telebirr
     * @param string|null $merchOrderId Optional merchant order ID from your system
     * @param string|null $refundReason Optional reason for refund
     * @param string|null $refundOrderId Optional unique refund request ID (will be auto-generated if null)
     * @return array
     * @throws TelebirrException
     */
    public function refundOrder($refundAmount, $paymentOrderId = null, $merchOrderId = null, $refundReason = null, $refundOrderId = null)
    {
        if (empty($paymentOrderId) && empty($merchOrderId)) {
            throw new TelebirrException('Either paymentOrderId or merchOrderId must be provided for refund.');
        }

        // Format amount to 2 decimal places as required by Telebirr
        $refundAmount = number_format((float)$refundAmount, 2, '.', '');

        // Get fabric token
        $tokenService = new FabricTokenService(
            $this->baseUrl,
            $this->fabricAppId,
            $this->appSecret,
            $this->merchantAppId
        );

        $tokenResult = json_decode($tokenService->applyFabricToken(), true);

        if (!isset($tokenResult['token'])) {
            throw new TelebirrException('Failed to get fabric token for refunding');
        }

        $fabricToken = $tokenResult['token'];

        // Request refund from Telebirr API
        $response = $this->requestRefund($fabricToken, $refundAmount, $paymentOrderId, $merchOrderId, $refundReason, $refundOrderId);
        
        $result = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new TelebirrException('Invalid JSON response from Telebirr: ' . json_last_error_msg());
        }

        return $result;
    }

    /**
     * Request refund from Telebirr API
     *
     * @param string $fabricToken
     * @param string $refundAmount
     * @param string|null $paymentOrderId
     * @param string|null $merchOrderId
     * @param string|null $refundReason
     * @param string|null $refundOrderId
     * @return string
     * @throws TelebirrException
     */
    protected function requestRefund($fabricToken, $refundAmount, $paymentOrderId = null, $merchOrderId = null, $refundReason = null, $refundOrderId = null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl . '/payment/v1/merchant/refund');
        curl_setopt($ch, CURLOPT_POST, 1);

        $headers = [
            "Content-Type: application/json",
            "X-APP-Key: " . $this->fabricAppId,
            "Authorization: " . $fabricToken
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $payload = $this->createRefundRequestObject($refundAmount, $paymentOrderId, $merchOrderId, $refundReason, $refundOrderId);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, config('telebirr.ssl_verify', true));
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, config('telebirr.ssl_verify', true) ? 2 : false);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new TelebirrException('cURL Error (refund): ' . $error);
        }

        curl_close($ch);

        return $response;
    }

    /**
     * Create refund request object
     *
     * @param string $refundAmount
     * @param string|null $paymentOrderId
     * @param string|null $merchOrderId
     * @param string|null $refundReason
     * @param string|null $refundOrderId
     * @return string
     */
    protected function createRefundRequestObject($refundAmount, $paymentOrderId = null, $merchOrderId = null, $refundReason = null, $refundOrderId = null)
    {
        $req = [
            'nonce_str' => SignatureHelper::createNonceStr(),
            'method' => 'payment.refund',
            'timestamp' => SignatureHelper::createTimeStamp(),
            'version' => '1.0',
            'biz_content' => [],
        ];

        // Generate refund_request_no if not provided
        $refundRequestNo = !empty($refundOrderId)
            ? $refundOrderId
            : SignatureHelper::createMerchantOrderId();

        $biz = [
            'appid' => $this->merchantAppId,
            'merch_code' => $this->merchantCode,
            'refund_amount' => $refundAmount,
            'refund_request_no' => $refundRequestNo,
        ];

        if (!empty($paymentOrderId)) {
            $biz['payment_order_id'] = $paymentOrderId;
        }

        if (!empty($merchOrderId)) {
            $biz['merch_order_id'] = $merchOrderId;
        }

        if (!empty($refundReason)) {
            $biz['refund_reason'] = $refundReason;
        }

        if ($refundOrderId !== null && $refundOrderId !== '') {
            $biz['refund_order_id'] = $refundOrderId;
        }

        $req['biz_content'] = $biz;
        $req['sign_type'] = 'SHA256WithRSA';
        $req['sign'] = SignatureHelper::sign($req, $this->privateKeyPath);

        return json_encode($req);
    }
}
