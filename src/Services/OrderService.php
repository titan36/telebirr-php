<?php

namespace Ttechnos\Telebirr\Services;

use Ttechnos\Telebirr\Exceptions\TelebirrException;
use Ttechnos\Telebirr\Utils\SignatureHelper;

class OrderService
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
     * Create an order
     *
     * @param string $title
     * @param float|string $amount
     * @param array $options
     * @return array
     * @throws TelebirrException
     */
    public function createOrder($title, $amount, array $options = [])
    {
        // Get fabric token
        $tokenService = new FabricTokenService(
            $this->baseUrl,
            $this->fabricAppId,
            $this->appSecret,
            $this->merchantAppId
        );

        $tokenResult = json_decode($tokenService->applyFabricToken(), true);

        if (!isset($tokenResult['token'])) {
            throw new TelebirrException('Failed to get fabric token');
        }

        $fabricToken = $tokenResult['token'];

        // Create pre-order
        $createOrderResult = $this->requestCreateOrder($fabricToken, $title, $amount, $options);
        
        $orderData = json_decode($createOrderResult, true);

        if (!isset($orderData['biz_content']['prepay_id'])) {
            throw new TelebirrException('Failed to create order: ' . ($orderData['msg'] ?? 'Unknown error'));
        }

        $prepayId = $orderData['biz_content']['prepay_id'];

        // Create raw request for payment
        $rawRequest = $this->createRawRequest($prepayId);

        return [
            'prepay_id' => $prepayId,
            'raw_request' => $rawRequest,
            'order_data' => $orderData,
        ];
    }

    /**
     * Request create order from Telebirr API
     *
     * @param string $fabricToken
     * @param string $title
     * @param float|string $amount
     * @param array $options
     * @return string
     * @throws TelebirrException
     */
    protected function requestCreateOrder($fabricToken, $title, $amount, array $options = [])
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl . '/payment/v1/merchant/preOrder');
        curl_setopt($ch, CURLOPT_POST, 1);

        $headers = [
            "Content-Type: application/json",
            "X-APP-Key: " . $this->fabricAppId,
            "Authorization: " . $fabricToken
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $payload = $this->createRequestObject($title, $amount, $options);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, config('telebirr.ssl_verify', true));
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, config('telebirr.ssl_verify', true) ? 2 : false);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new TelebirrException('cURL Error: ' . $error);
        }

        curl_close($ch);

        return $response;
    }

    /**
     * Create request object
     *
     * @param string $title
     * @param float|string $amount
     * @param array $options
     * @return string
     */
    protected function createRequestObject($title, $amount, array $options = [])
    {
        $notifyUrl = $options['notify_url'] ?? config('telebirr.notify_url');

        $req = [
            'nonce_str' => SignatureHelper::createNonceStr(),
            'method' => 'payment.preorder',
            'timestamp' => SignatureHelper::createTimeStamp(),
            'version' => '1.0',
            'biz_content' => [],
        ];

        $biz = [
            'notify_url' => $options['notify_url'] ?? config('telebirr.notify_url'),
            'appid' => $this->merchantAppId,
            'merch_code' => $this->merchantCode,
            'merch_order_id' => $this->createMerchantOrderId(),
            'trade_type' => 'Checkout',
            'title' => $title,
            'total_amount' => (string)$amount,
            'trans_currency' => 'ETB',
            'timeout_express' => config('telebirr.timeout', '120m'),
            'business_type' => $options['business_type'] ?? 'BuyGoods',
            'payee_identifier' => $this->merchantCode,
            'payee_identifier_type' => $options['payee_identifier_type'] ?? '04',
            'payee_type' => '5000',
            'redirect_url' => $options['redirect_url'] ?? config('telebirr.redirect_url'),
            'callback_info' => $options['callback_info'] ?? 'From web',
        ];

        if (isset($options['redirect_url'])) {
            $biz['redirect_url'] = $options['redirect_url'];
        }

        $req['biz_content'] = $biz;
        $req['sign_type'] = 'SHA256WithRSA';
        $req['sign'] = SignatureHelper::sign($req, $this->privateKeyPath);

        return json_encode($req);
    }

    /**
     * Create raw request string for payment
     *
     * @param string $prepayId
     * @return string
     */
    protected function createRawRequest($prepayId)
    {
        $maps = [
            "appid" => $this->merchantAppId,
            "merch_code" => $this->merchantCode,
            "nonce_str" => SignatureHelper::createNonceStr(),
            "prepay_id" => $prepayId,
            "timestamp" => SignatureHelper::createTimeStamp(),
            "sign_type" => "SHA256WithRSA"
        ];

        $rawRequest = '';
        foreach ($maps as $key => $value) {
            $rawRequest .= $key . '=' . $value . "&";
        }

        $sign = SignatureHelper::sign($maps, $this->privateKeyPath);
        $rawRequest = $rawRequest . 'sign=' . $sign;

        return $rawRequest;
    }

    /**
     * Create merchant order ID
     *
     * @return string
     */
    protected function createMerchantOrderId()
    {
        return (string)time();
    }
}