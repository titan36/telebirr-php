<?php

namespace Ttechnos\Telebirr;

use Ttechnos\Telebirr\Services\FabricTokenService;
use Ttechnos\Telebirr\Services\OrderService;
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
     * Verify callback signature
     *
     * @param array $data
     * @return bool
     */
    public function verifySignature(array $data)
    {
        // Implement signature verification logic
        return true;
    }
}