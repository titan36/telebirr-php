<?php

namespace Ttechnos\Telebirr\Facades;


use Illuminate\Support\Facades\Facade;

/**
 * @method static array createOrder(string $title, float|string $amount, array $options = [])
 * @method static string getFabricToken()
 * @method static bool verifySignature(array $data)
 *
 * @see \Ttechnos\Telebirr\TelebirrService
 */
class Telebirr extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'telebirr';
    }
}