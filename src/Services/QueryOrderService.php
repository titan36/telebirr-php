<?php

namespace Ttechnos\Telebirr\Services;

use Ttechnos\Telebirr\Exceptions\TelebirrException;
use Ttechnos\Telebirr\Utils\SignatureHelper;

class QueryOrderService
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
     * Query order status from Telebirr
     *
     * @param string|null $prepayId
     * @param string|null $merchOrderId
     * @return array
     * @throws TelebirrException
     */
    public function queryOrder($prepayId = null, $merchOrderId = null)
    {
        if (empty($prepayId) && empty($merchOrderId)) {
            throw new TelebirrException('Either prepayId or merchOrderId must be provided to query order.');
        }

        // Get fabric token
        $tokenService = new FabricTokenService(
            $this->baseUrl,
            $this->fabricAppId,
            $this->appSecret,
            $this->merchantAppId
        );

        $tokenResult = json_decode($tokenService->applyFabricToken(), true);

        if (!isset($tokenResult['token'])) {
            throw new TelebirrException('Failed to get fabric token for querying order');
        }

        $fabricToken = $tokenResult['token'];

        // Request query order from Telebirr API
        $response = $this->requestQueryOrder($fabricToken, $prepayId, $merchOrderId);
        
        $result = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new TelebirrException('Invalid JSON response from Telebirr: ' . json_last_error_msg());
        }

        return $result;
    }

    /**
     * Request query order from Telebirr API
     *
     * @param string $fabricToken
     * @param string|null $prepayId
     * @param string|null $merchOrderId
     * @return string
     * @throws TelebirrException
     */
    protected function requestQueryOrder($fabricToken, $prepayId = null, $merchOrderId = null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl . '/payment/v1/merchant/queryOrder');
        curl_setopt($ch, CURLOPT_POST, 1);

        $headers = [
            "Content-Type: application/json",
            "X-APP-Key: " . $this->fabricAppId,
            "Authorization: " . $fabricToken
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $payload = $this->createQueryRequestObject($prepayId, $merchOrderId);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, config('telebirr.ssl_verify', true));
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, config('telebirr.ssl_verify', true) ? 2 : false);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new TelebirrException('cURL Error (queryOrder): ' . $error);
        }

        curl_close($ch);

        return $response;
    }

    /**
     * Create query request object
     *
     * @param string|null $prepayId
     * @param string|null $merchOrderId
     * @return string
     */
    protected function createQueryRequestObject($prepayId = null, $merchOrderId = null)
    {
        $req = [
            'nonce_str' => SignatureHelper::createNonceStr(),
            'method' => 'payment.queryorder',
            'timestamp' => SignatureHelper::createTimeStamp(),
            'version' => '1.0',
            'biz_content' => [],
        ];

        $biz = [
            'appid' => $this->merchantAppId,
            'merch_code' => $this->merchantCode,
        ];

        if (!empty($prepayId)) {
            $biz['prepay_id'] = $prepayId;
        }

        if (!empty($merchOrderId)) {
            $biz['merch_order_id'] = $merchOrderId;
        }

        $req['biz_content'] = $biz;
        $req['sign_type'] = 'SHA256WithRSA';
        $req['sign'] = SignatureHelper::sign($req, $this->privateKeyPath);

        return json_encode($req);
    }
}
