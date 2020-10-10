<?php

namespace Maksa988\WayForPay\Domain;

use \WayForPay\SDK\Domain\Card as WayForPayCard;
use WayForPay\SDK\Domain\CardToken;

class Card
{
    /**
     * @var string
     */
    protected $card;

    /**
     * @var int
     */
    protected $month;

    /**
     * @var int
     */
    protected $year;

    /**
     * @var int
     */
    protected $cvv;

    /**
     * @var string
     */
    protected $holder;

    /**
     * @var string
     */
    protected $token;

    /**
     * Card constructor.
     * @param string $card
     * @param int|null $month
     * @param int|null $year
     * @param int|null $cvv
     * @param string|null $holder
     */
    public function __construct($card, $month = null, $year = null, $cvv = null, $holder = null)
    {
        $this->card = $card;
        $this->month = $month;
        $this->year = $year;
        $this->cvv = $cvv;
        $this->holder = $holder;

        $this->token = $card;
    }

    /**
     * @return bool
     */
    public function isToken()
    {
        return is_null($this->month)
            || is_null($this->year)
            || is_null($this->cvv)
            || is_null($this->holder);
    }

    /**
     * @return \WayForPay\SDK\Domain\Card
     */
    public function getCard()
    {
        return new WayForPayCard($this->card, $this->month, $this->year, $this->cvv, $this->holder);
    }

    /**
     * @return CardToken
     */
    public function getCardToken()
    {
        return new CardToken($this->token);
    }
}
