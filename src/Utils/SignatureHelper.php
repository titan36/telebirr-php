<?php

namespace Ttechnos\Telebirr\Utils;

use phpseclib3\Crypt\RSA;
use phpseclib3\Crypt\PublicKeyLoader;


class SignatureHelper
{
    /**
     * Sign request data with RSA
     *
     * @param array $request
     * @param string $privateKeyPath
     * @return string
     */
    public static function sign(array $request, $privateKeyPath)
    {
        $excludeFields = ["sign", "sign_type", "header", "refund_info", "openType", "raw_request"];
        $data = $request;
        ksort($data);

        $stringApplet = '';
        foreach ($data as $key => $values) {
            if (in_array($key, $excludeFields)) {
                continue;
            }

            if ($key == "biz_content") {
                foreach ($values as $value => $singleValue) {
                    if ($stringApplet == '') {
                        $stringApplet = $value . '=' . $singleValue;
                    } else {
                        $stringApplet = $stringApplet . '&' . $value . '=' . $singleValue;
                    }
                }
            } else {
                if ($stringApplet == '') {
                    $stringApplet = $key . '=' . $values;
                } else {
                    $stringApplet = $stringApplet . '&' . $key . '=' . $values;
                }
            }
        }

        $sortedString = self::sortedString($stringApplet);

        return self::signWithRSA($sortedString, $privateKeyPath);
    }

    /**
     * Sort string alphabetically
     *
     * @param string $stringApplet
     * @return string
     */
    protected static function sortedString($stringApplet)
    {
        $stringExplode = '';
        $sortedArray = explode("&", $stringApplet);
        sort($sortedArray);

        foreach ($sortedArray as $value) {
            if ($stringExplode == '') {
                $stringExplode = $value;
            } else {
                $stringExplode = $stringExplode . '&' . $value;
            }
        }

        return $stringExplode;
    }

    /**
     * Sign data with RSA private key
     *
     * @param string $data
     * @param string $privateKeyPath
     * @return string
     */
    protected static function signWithRSA($data, $privateKeyPath)
    {
        $privateKeyContent = file_get_contents($privateKeyPath);
        
        try {
            $privateKey = PublicKeyLoader::load($privateKeyContent);
        } catch (\Exception $e) {
            throw new \Exception("Error loading Private Key: " . $e->getMessage());
        }

        $signatureByte = $privateKey
            ->withHash('sha256')
            ->withMGFHash('sha256')
            ->withPadding(RSA::SIGNATURE_PSS)
            ->sign($data);

        return base64_encode($signatureByte);
    }

    /**
     * Create timestamp
     *
     * @return string
     */
    public static function createTimeStamp()
    {
        return (string)time();
    }

    /**
     * Create nonce string
     *
     * @return string
     */
    public static function createNonceStr()
    {
        $chars = [
            "0", "1", "2", "3", "4", "5", "6", "7", "8", "9",
            "A", "B", "C", "D", "E", "F", "G", "H", "I", "J",
            "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T",
            "U", "V", "W", "X", "Y", "Z"
        ];

        $str = "";
        for ($i = 0; $i < 32; $i++) {
            $index = rand(0, 35);
            $str .= $chars[$index];
        }

        return $str;
    }

    /**
     * Create merchant order ID
     *
     * @return string
     */
    public static function createMerchantOrderId()
    {
        return (string)time();
    }
}