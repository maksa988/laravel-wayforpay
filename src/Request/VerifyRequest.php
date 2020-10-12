<?php
/*
 * This file is part of the WayForPay project.
 *
 * @link https://github.com/wayforpay/php-sdk
 *
 * @author Vladislav Lyshenko <vladdnepr1989@gmail.com>
 * @copyright Copyright 2019 WayForPay
 * @license   https://opensource.org/licenses/MIT
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Maksa988\WayForPay\Request;

use Illuminate\Support\Arr;
use Maksa988\WayForPay\Response\VerifyResponse;
use WayForPay\SDK\Credential\AccountSecretCredential;
use WayForPay\SDK\Domain\Card;
use WayForPay\SDK\Domain\CardToken;
use WayForPay\SDK\Domain\Client;
use WayForPay\SDK\Domain\MerchantTypes;
use WayForPay\SDK\Request\ApiRequest;

/**
 * Class VerifyRequest
 * @package Maksa988\WayForPay\Request
 * @method VerifyResponse send()
 */
class VerifyRequest extends ApiRequest
{
    /**
     * @var string
     */
    private $merchantAuthType;

    /**
     * @var string
     */
    private $merchantDomainName;

    /**
     * @var string
     */
    private $serviceUrl;

    /**
     * @var string
     */
    private $orderReference;

    /**
     * @var float
     */
    private $amount;

    /**
     * @var string
     */
    private $currency;

    /**
     * @var Card|null
     */
    private $card;

    /**
     * @var Client
     */
    private $client;

    /**
     * VerifyRequest constructor.
     *
     * @param AccountSecretCredential $credential
     * @param Card|CardToken $card
     * @param string $orderReference
     * @param float $amount
     * @param string $currency
     * @param string $merchantDomainName
     * @param Client $client
     * @param string $serviceUrl
     * @param string $merchantAuthType
     */
    public function __construct(
        AccountSecretCredential $credential,
        $card,
        $orderReference,
        $amount, $currency,
        $merchantDomainName,
        Client $client = null,
        $serviceUrl = null,
        $merchantAuthType = MerchantTypes::AUTH_SIMPLE_SIGNATURE
    ) {
        parent::__construct($credential);

        if ($card instanceof Card) {
            $this->card = $card;
        } else {
            throw new \InvalidArgumentException('Card required');
        }

        if ($merchantAuthType && !in_array($merchantAuthType, $this->merchantAuthTypeAllowed)) {
            throw new \InvalidArgumentException(
                'Unexpected auth type, expected ' . implode(', ', $this->merchantAuthTypeAllowed)
                . ', got ' . $merchantAuthType
            );
        }

        if (strlen($currency) !== 3) {
            throw new \InvalidArgumentException('Currency must contain 3 chars');
        }

        $this->merchantAuthType = strval($merchantAuthType ?? MerchantTypes::AUTH_SIMPLE_SIGNATURE);
        $this->merchantDomainName = strval($merchantDomainName);
        $this->serviceUrl = strval($serviceUrl);
        $this->orderReference = strval($orderReference);
        $this->amount = floatval($amount);
        $this->currency = strtoupper(strval($currency));
        $this->client = $client ?: new Client();
    }

    /**
     * @return array
     */
    public function getRequestSignatureFieldsValues()
    {
        return array_merge(parent::getRequestSignatureFieldsValues(), array(
            'merchantDomainName' => $this->merchantDomainName,
            'orderReference' => $this->orderReference,
            'amount' => $this->amount,
            'currency' => $this->currency,
        ));
    }

    /**
     * @return array
     */
    public function getResponseSignatureFieldsRequired()
    {
        return array(
            'merchantAccount',
            'orderReference',
            'amount',
            'currency',
            'authCode',
            'cardPan',
            'transactionStatus',
            'reasonCode',
        );
    }

    /**
     * @return string
     */
    public function getTransactionType()
    {
        return 'VERIFY';
    }

    /**
     * @return array
     */
    public function getTransactionData()
    {
        $data = array_merge(parent::getTransactionData(), [
            'merchantAuthType' => $this->merchantAuthType,
            'merchantDomainName' => $this->merchantDomainName,
            'serviceUrl' => $this->serviceUrl,
            'orderReference' => $this->orderReference,
            'amount' => $this->amount,
            'currency' => $this->currency,

            'clientFirstName' => $this->client->getNameFirst(),
            'clientLastName' => $this->client->getNameLast(),
            'clientEmail' => $this->client->getEmail(),
            'clientPhone' => $this->client->getPhone(),
            'clientCountry' => $this->client->getCountry(),
            'clientIpAddress' => $this->client->getIp(),
            'clientAddress' => $this->client->getAddress(),
            'clientCity' => $this->client->getCity(),
            'clientState' => $this->client->getState(),
        ]);

        if ($this->card) {
            $data = array_merge($data, array(
                'card' => $this->card->getCard(),
                'expMonth' => sprintf('%02d', $this->card->getMonth()),
                'expYear' => strval($this->card->getYear()),
                'cardCvv' => strval($this->card->getCvv()),
                'cardHolder' => strval($this->card->getHolder()),
            ));
        } else {
            throw new \RuntimeException('Card required');
        }

        return $data;
    }

    /**
     * @return string
     */
    public function getResponseClass()
    {
        return VerifyResponse::getClass();
    }
}
