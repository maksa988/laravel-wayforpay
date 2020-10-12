<?php
namespace Maksa988\WayForPay\Wizard;

use DateTime;
use Maksa988\WayForPay\Request\VerifyRequest;
use WayForPay\SDK\Collection\ProductCollection;
use WayForPay\SDK\Credential\AccountSecretCredential;
use WayForPay\SDK\Domain\Card;
use WayForPay\SDK\Domain\CardToken;
use WayForPay\SDK\Domain\Client;
use WayForPay\SDK\Wizard\RequestWizard;

class VerifyWizard extends RequestWizard
{
    /**
     * @var AccountSecretCredential
     */
    protected $credential;

    /**
     * @var Card
     */
    protected $card;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var ProductCollection
     */
    protected $products;

    /**
     * @var string
     */
    protected $orderReference;

    /**
     * @var float
     */
    protected $amount;

    /**
     * @var string
     */
    protected $currency;

    /**
     * @var string
     */
    protected $merchantDomainName;

    /**
     * @var string
     */
    protected $merchantAuthType;

    /**
     * @var string
     */
    protected $serviceUrl;

    protected $propertyRequired = array(
        'orderReference', 'merchantDomainName',
    );

    /**
     * @param AccountSecretCredential $credential
     * @return self
     */
    public static function get(AccountSecretCredential $credential)
    {
        return new self($credential);
    }

    public function __construct(AccountSecretCredential $credential)
    {
        $this->credential = $credential;
    }

    /**
     * @return VerifyRequest
     */
    public function getRequest()
    {
        $this->check();

        return new VerifyRequest(
            $this->credential,
            $this->card,
            $this->orderReference,
            $this->amount,
            $this->currency,
            $this->merchantDomainName,
            $this->client,
            $this->serviceUrl,
            $this->merchantAuthType
        );
    }

    /**
     * @param Card $card
     * @return VerifyWizard
     */
    public function setCard($card)
    {
        $this->card = $card;
        return $this;
    }
    /**
     * @param Client $client
     * @return VerifyWizard
     */
    public function setClient($client)
    {
        $this->client = $client;
        return $this;
    }

    /**
     * @param string $orderReference
     * @return VerifyWizard
     */
    public function setOrderReference($orderReference)
    {
        $this->orderReference = $orderReference;
        return $this;
    }

    /**
     * @param float $amount
     * @return VerifyWizard
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * @param string $currency
     * @return VerifyWizard
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * @param string $merchantDomainName
     * @return VerifyWizard
     */
    public function setMerchantDomainName($merchantDomainName)
    {
        $this->merchantDomainName = $merchantDomainName;
        return $this;
    }

    /**
     * @param string $merchantAuthType
     * @return VerifyWizard
     */
    public function setMerchantAuthType($merchantAuthType)
    {
        $this->merchantAuthType = $merchantAuthType;
        return $this;
    }

    /**
     * @param string $serviceUrl
     * @return VerifyWizard
     */
    public function setServiceUrl($serviceUrl)
    {
        $this->serviceUrl = $serviceUrl;
        return $this;
    }
}
