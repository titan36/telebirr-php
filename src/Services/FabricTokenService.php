<?php

namespace Ttechnos\Telebirr\Services;

use Afroeltechnologies\TelebirrLaravel\Exceptions\TelebirrException;

class FabricTokenService
{
    protected $baseUrl;
    protected $fabricAppId;
    protected $appSecret;
    protected $merchantAppId;

    public function __construct($baseUrl, $fabricAppId, $appSecret, $merchantAppId)
    {
        $this->baseUrl = $baseUrl;
        $this->fabricAppId = $fabricAppId;
        $this->appSecret = $appSecret;
        $this->merchantAppId = $merchantAppId;
    }

    /**
     * Apply for fabric token from Telebirr
     *
     * @return string JSON response containing token
     * @throws TelebirrException
     */
    public function applyFabricToken()
    {
        $ch = curl_init();

        $headers = [
            "Content-Type: application/json",
            "X-APP-Key: " . $this->fabricAppId
        ];

        curl_setopt($ch, CURLOPT_URL, $this->baseUrl . "/payment/v1/token");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADER, 0);

        $payload = [
            "appSecret" => $this->appSecret
        ];

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, config('telebirr.ssl_verify', true));
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, config('telebirr.ssl_verify', true) ? 2 : false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $authToken = curl_exec($ch);

        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new TelebirrException('Failed to get fabric token: ' . $error);
        }

        curl_close($ch);

        return $authToken;
    }
}