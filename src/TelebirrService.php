<?php

namespace Ttechnos\Telebirr;

use Ttechnos\Telebirr\Services\FabricTokenService;
use Ttechnos\Telebirr\Services\OrderService;
use Ttechnos\Telebirr\Services\QueryOrderService;
use Ttechnos\Telebirr\Services\RefundOrderService;
use Ttechnos\Telebirr\Utils\SignatureHelper;
use Ttechnos\Telebirr\Exceptions\TelebirrException;

class TelebirrService
{
    protected $baseUrl;
    protected $fabricAppId;
    protected $appSecret;
    protected $merchantAppId;
    protected $merchantCode;
    protected $privateKeyPath;
    protected $publicKeyPath;

    public function __construct(
        $baseUrl,
        $fabricAppId,
        $appSecret,
        $merchantAppId,
        $merchantCode,
        $privateKeyPath,
        $publicKeyPath
    ) {
        $this->baseUrl = $baseUrl;
        $this->fabricAppId = $fabricAppId;
        $this->appSecret = $appSecret;
        $this->merchantAppId = $merchantAppId;
        $this->merchantCode = $merchantCode;
        $this->privateKeyPath = $privateKeyPath;
        $this->publicKeyPath = $publicKeyPath;
    }

    /**
     * Create a payment order
     *
     * @param string $title Order title/description
     * @param float|string $amount Payment amount
     * @param array $options Additional options
     * @return array
     * @throws TelebirrException
     */
    public function createOrder($title, $amount, array $options = [])
    {
        $orderService = new OrderService(
            $this->baseUrl,
            $this->fabricAppId,
            $this->appSecret,
            $this->merchantAppId,
            $this->merchantCode,
            $this->privateKeyPath
        );

        return $orderService->createOrder($title, $amount, $options);
    }

    /**
     * Get fabric token
     *
     * @return string
     * @throws TelebirrException
     */
    public function getFabricToken()
    {
        $tokenService = new FabricTokenService(
            $this->baseUrl,
            $this->fabricAppId,
            $this->appSecret,
            $this->merchantAppId
        );

        return $tokenService->applyFabricToken();
    }

    /**
     * Query order status
     *
     * @param string|null $prepayId
     * @param string|null $merchOrderId
     * @return array
     * @throws TelebirrException
     */
    public function queryOrder($prepayId = null, $merchOrderId = null)
    {
        $queryService = new QueryOrderService(
            $this->baseUrl,
            $this->fabricAppId,
            $this->appSecret,
            $this->merchantAppId,
            $this->merchantCode,
            $this->privateKeyPath
        );

        return $queryService->queryOrder($prepayId, $merchOrderId);
    }

    /**
     * Refund a transaction
     *
     * @param string|int|float $refundAmount
     * @param string|null $paymentOrderId
     * @param string|null $merchOrderId
     * @param string|null $refundReason
     * @param string|null $refundOrderId
     * @return array
     * @throws TelebirrException
     */
    public function refundOrder($refundAmount, $paymentOrderId = null, $merchOrderId = null, $refundReason = null, $refundOrderId = null)
    {
        $refundService = new RefundOrderService(
            $this->baseUrl,
            $this->fabricAppId,
            $this->appSecret,
            $this->merchantAppId,
            $this->merchantCode,
            $this->privateKeyPath
        );

        return $refundService->refundOrder($refundAmount, $paymentOrderId, $merchOrderId, $refundReason, $refundOrderId);
    }

    /**
     * Verify callback signature
     *
     * @param array $data
     * @return bool
     */
    public function verifySignature(array $data)
    {
        if (!isset($data['sign'])) {
            return false;
        }

        $signature = $data['sign'];

        return SignatureHelper::verify($data, $signature, $this->publicKeyPath);
    }
}