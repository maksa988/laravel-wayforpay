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

namespace Maksa988\WayForPay\Response;

use WayForPay\SDK\Domain\Transaction;
use WayForPay\SDK\Response\Response;

class VerifyResponse extends Response
{
    /**
     * @var Transaction
     */
    private $transaction;

    /**
     * VerifyResponse constructor.
     * @param array $data
     * @throws \Exception
     */
    public function __construct(array $data)
    {
        parent::__construct($data);

        $this->transaction = Transaction::fromArray($data);
    }

    /**
     * @return Transaction
     */
    public function getTransaction()
    {
        return $this->transaction;
    }
}
