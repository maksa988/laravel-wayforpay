<?php

return [
    /*
     * Test mode for using test credentials
     */
    'testMode' => env("WAYFORPAY_TEST", true),

    /*
     * Merchant domain
     */
    'merchantDomain' => env('WAYFORPAY_DOMAIN', 'test.shop'),

    /*
     * Merchant Account ID
     */
    'merchantAccount' => env('WAYFORPAY_ACCOUNT', 'test_merch_n1'),

    /*
     * Merchant Secret key
     */
    'merchantSecretKey' => env('WAYFORPAY_SECRET_KEY', 'flk3409refn54t54t*FNJRET'),
];
