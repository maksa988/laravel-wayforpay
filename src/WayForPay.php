<?php

namespace Maksa988\WayForPay;

use Illuminate\Contracts\Support\Arrayable;
use InvalidArgumentException;
use Maksa988\WayForPay\Collection\ProductCollection;
use Maksa988\WayForPay\Domain\Card;
use Maksa988\WayForPay\Domain\Languages;
use Maksa988\WayForPay\Domain\PaymentSystems;
use Maksa988\WayForPay\Wizard\VerifyWizard;
use WayForPay\SDK\Credential\AccountSecretCredential;
use WayForPay\SDK\Credential\AccountSecretTestCredential;
use WayForPay\SDK\Domain\Client;
use WayForPay\SDK\Domain\MerchantTypes;
use WayForPay\SDK\Form\PurchaseForm;
use WayForPay\SDK\Handler\ServiceUrlHandler;
use WayForPay\SDK\Response\ChargeResponse;
use WayForPay\SDK\Response\InvoiceResponse;
use WayForPay\SDK\Wizard\ChargeWizard;
use WayForPay\SDK\Wizard\CheckWizard;
use WayForPay\SDK\Wizard\Complete3DSWizard;
use WayForPay\SDK\Wizard\InvoiceWizard;
use WayForPay\SDK\Wizard\PurchaseWizard;
use WayForPay\SDK\Wizard\RefundWizard;

class WayForPay
{
    /**
     * @var bool
     */
    protected $testMode = false;

    /**
     * @var AccountSecretTestCredential
     */
    private $credentials;

    /**
     * @var string
     */
    private $merchantDomain;
    /**
     * @var string
     */
    private $merchantAccount;

    /**
     * @var string
     */
    private $merchantPassword;

    /**
     * Init
     */
    public function __construct()
    {
        $this->merchantAccount = config('wayforpay.merchantAccount');
        $this->merchantPassword = config('wayforpay.merchantSecretKey');

        $this->setMerchantDomain(config('wayforpay.merchantDomain'));

        $this->testMode = config('wayforpay.testMode', true);

        $this->initCredentials();
    }

    /**
     * @return $this
     */
    public function initCredentials()
    {
        if($this->testMode) {
            return $this->setCredentials(new AccountSecretTestCredential());
        }

        return $this->setCredentials(new AccountSecretCredential($this->merchantAccount, $this->merchantPassword));
    }

    /**
     * @param AccountSecretCredential $credential
     * @return $this
     */
    public function setCredentials(AccountSecretCredential $credential)
    {
        $this->credentials = $credential;

        return $this;
    }

    /**
     * @return AccountSecretTestCredential
     */
    public function getCredentials()
    {
        return $this->credentials;
    }

    /**
     * @param string $domain
     * @return $this
     */
    public function setMerchantDomain($domain)
    {
        $this->merchantDomain = $domain;

        return $this;
    }

    /**
     * @return string
     */
    public function getMerchantDomain()
    {
        return $this->merchantDomain;
    }

    /**
     * @param string $order_id
     * @param float $amount
     * @param Client $client
     * @param ProductCollection $products
     * @param Card $card
     * @param string $currency
     * @param \DateTime $date
     * @param null|int $holdTimeout
     * @param null|string $serviceUrl
     * @param null|string $socialUri
     * @param string $transactionType
     * @param string $transactionSecureType
     * @return ChargeResponse
     *
     * @throws \Exception
     */
    public function charge(
        $order_id,
        $amount,
        Client $client,
        ProductCollection $products,
        Card $card,
        $currency = "USD",
        \DateTime $date = null,
        $holdTimeout = null,
        $serviceUrl = null,
        $socialUri = null,
        $transactionType = MerchantTypes::TRANSACTION_AUTO,
        $transactionSecureType = MerchantTypes::TRANSACTION_SECURE_AUTO)
    {
        $wizard = ChargeWizard::get($this->getCredentials())
            ->setOrderReference($order_id)
            ->setAmount($amount)
            ->setCurrency($currency)
            ->setOrderDate($date ?? (new \DateTime()))
            ->setMerchantDomainName($this->getMerchantDomain())
            ->setClient($client)
            ->setProducts($products)
            ->setMerchantTransactionType($transactionType)
            ->setMerchantTransactionSecureType($transactionSecureType)
            ->setServiceUrl($serviceUrl)
            ->setHoldTimeout($holdTimeout)
            ->setSocialUri($socialUri);

        if($card->isToken()) {
            $wizard->setCardToken($card->getCardToken());
        } else {
            $wizard->setCard($card->getCard());
        }

        return $wizard->getRequest()->send();
    }

    /**
     * @param string $order_id
     * @return \WayForPay\SDK\Response\CheckResponse
     */
    public function check($order_id)
    {
        return CheckWizard::get($this->getCredentials())
            ->setOrderReference($order_id)
            ->getRequest()
            ->send();
    }

    /**
     * @param string $authTicket
     * @param string $d3Md
     * @param string $d3Pares
     * @return \WayForPay\SDK\Response\Complete3DSResponse
     */
    public function complete3ds($authTicket, $d3Md, $d3Pares)
    {
        return Complete3DSWizard::get($this->getCredentials())
            ->setAuthTicket($authTicket)
            ->setD3Md($d3Md)
            ->setD3Pares($d3Pares)
            ->getRequest()
            ->send();
    }

    /**
     * @param string $order_id
     * @param float $amount
     * @param Client $client
     * @param ProductCollection $products
     * @param string $currency
     * @param \DateTime|null $date
     * @param null|string $serviceUrl
     * @param PaymentSystems|null $paymentSystems
     * @param null|int $holdTimeout
     * @param null|int $orderTimeout
     * @param null|int $orderLifetime
     * @return InvoiceResponse
     * @throws \Exception
     */
    public function createInvoice(
        $order_id,
        $amount,
        Client $client,
        ProductCollection $products,
        $currency = "USD",
        \DateTime $date = null,
        $serviceUrl = null,
        PaymentSystems $paymentSystems = null,
        $holdTimeout = null,
        $orderTimeout = null,
        $orderLifetime = null)
    {
        $wizard = InvoiceWizard::get($this->getCredentials())
            ->setOrderReference($order_id)
            ->setAmount($amount)
            ->setCurrency($currency)
            ->setMerchantDomainName($this->getMerchantDomain())
            ->setClient($client)
            ->setProducts($products)
            ->setServiceUrl($serviceUrl)
            ->setOrderDate($date ?? (new \DateTime()))
            ->setHoldTimeout($holdTimeout)
            ->setOrderTimeout($orderTimeout)
            ->setOrderLifetime($orderLifetime);

        if($paymentSystems) {
            $wizard->setPaymentSystems($paymentSystems);
        }

        return $wizard->getRequest()->send();
    }

    /**
     * @param string $order_id
     * @param float $amount
     * @param Client $client
     * @param ProductCollection $products
     * @param string $currency
     * @param \DateTime|null $date
     * @param string $language
     * @param null|string $orderNo
     * @param null|string $returnUrl
     * @param null|string $serviceUrl
     * @param null|string $socialUri
     * @param string $transactionType
     * @param string $transactionSecureType
     * @param null|int $holdTimeout
     * @param null|int $orderTimeout
     * @param null|int $orderLifetime
     * @return PurchaseForm
     * @throws \Exception
     */
    public function purchase(
        $order_id,
        $amount,
        Client $client,
        ProductCollection $products,
        $currency = "USD",
        \DateTime $date = null,
        $language = Languages::AUTO,
        $orderNo = null,
        $returnUrl = null,
        $serviceUrl = null,
        $socialUri = null,
        $transactionType = MerchantTypes::TRANSACTION_AUTO,
        $transactionSecureType = MerchantTypes::TRANSACTION_SECURE_AUTO,
        $holdTimeout = null,
        $orderTimeout = null,
        $orderLifetime = null)
    {
        return PurchaseWizard::get($this->getCredentials())
            ->setOrderReference($order_id)
            ->setAmount($amount)
            ->setCurrency($currency)
            ->setOrderDate($date ?? (new \DateTime()))
            ->setMerchantDomainName($this->getMerchantDomain())
            ->setClient($client)
            ->setProducts($products)
            ->setMerchantTransactionType($transactionType)
            ->setMerchantTransactionSecureType($transactionSecureType)
            ->setReturnUrl($returnUrl)
            ->setServiceUrl($serviceUrl)
            ->setHoldTimeout($holdTimeout)
            ->setSocialUri($socialUri)
            ->setOrderTimeout($orderTimeout)
            ->setOrderLifetime($orderLifetime)
            ->setOrderNo($orderNo)
            ->setLanguage($language)
            ->getForm();
    }

    /**
     * @param string $order_id
     * @param float $amount
     * @param string $currency
     * @param null|string $comment
     * @return \WayForPay\SDK\Response\RufundResponse
     */
    public function refund($order_id, $amount, $currency = "USD", $comment = null)
    {
        return RefundWizard::get($this->getCredentials())
            ->setOrderReference($order_id)
            ->setAmount($amount)
            ->setCurrency($currency)
            ->setComment($comment)
            ->getRequest()
            ->send();
    }

    /**
     * @param string $order_id
     * @param Client $client
     * @param Card $card
     * @param int $amount
     * @param string $currency
     * @param null|string $serviceUrl
     * @return Response\VerifyResponse
     */
    public function verify($order_id,
       Client $client,
       Card $card,
       $amount = 0,
       $currency = "USD",
       $serviceUrl = null)
    {
        $wizard = VerifyWizard::get($this->getCredentials())
            ->setOrderReference($order_id)
            ->setAmount($amount)
            ->setCurrency($currency)
            ->setMerchantDomainName($this->getMerchantDomain())
            ->setClient($client)
            ->setServiceUrl($serviceUrl);

        if($card->isToken()) {
            throw new InvalidArgumentException("Only cards allowed");
        } else {
            $wizard->setCard($card->getCard());
        }

        return $wizard->getRequest()->send();
    }

    /**
     * @param array|Arrayable $request
     * @param \Closure $callback
     * @return string|\WayForPay\SDK\Domain\TransactionService
     * @throws \Exception
     */
    public function handleServiceUrl($request, \Closure $callback)
    {
        $handler = new ServiceUrlHandler($this->getCredentials());

        $request = ($request instanceof Arrayable) ? $request->toArray() : (array) $request;

        $response = $handler->parseRequestFromArray($request);

        return $callback($response->getTransaction(), function () use ($handler, $response) {
            return $handler->getSuccessResponse($response->getTransaction());
        });
    }
}
