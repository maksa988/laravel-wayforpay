<?php

namespace Maksa988\WayForPay\Facades;

use Illuminate\Support\Facades\Facade;
use Maksa988\WayForPay\Domain\Card;
use Maksa988\WayForPay\Domain\Client;
use Maksa988\WayForPay\Domain\Languages;
use Maksa988\WayForPay\Response\VerifyResponse;
use WayForPay\SDK\Collection\ProductCollection;
use WayForPay\SDK\Domain\MerchantTypes;
use WayForPay\SDK\Form\PurchaseForm;
use WayForPay\SDK\Response\ChargeResponse;
use WayForPay\SDK\Response\RufundResponse;

/**
 * Class WayForPay
 * @package Maksa988\WayForPay\Facades
 * @method VerifyResponse verify($order_id, Client $client, Card $card, $amount = 0, $currency = "USD", $serviceUrl = null)
 * @method ChargeResponse charge($order_id, $amount, Client $client, ProductCollection $products, Card $card, $currency = "USD", \DateTime $date = null, $holdTimeout = null, $serviceUrl = null, $socialUri = null, $transactionType = MerchantTypes::TRANSACTION_AUTO, $transactionSecureType = MerchantTypes::TRANSACTION_SECURE_AUTO)
 * @method PurchaseForm purchase($order_id, $amount, Client $client, ProductCollection $products, $currency = "USD", \DateTime $date = null, $language = Languages::AUTO, $orderNo = null, $returnUrl = null, $serviceUrl = null, $socialUri = null, $transactionType = MerchantTypes::TRANSACTION_AUTO, $transactionSecureType = MerchantTypes::TRANSACTION_SECURE_AUTO, $holdTimeout = null, $orderTimeout = null, $orderLifetime = null)
 * @method RufundResponse refund($order_id, $amount, $currency = "USD", $comment = null)
 * @method string handleServiceUrl($request, \Closure $callback)
 */
class WayForPay extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'wayforpay';
    }
}
