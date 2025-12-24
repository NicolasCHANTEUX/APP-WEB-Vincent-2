<?php

namespace Config;

class Paypal
{
    /**
     * Use PayPal sandbox for testing. Set to false for production.
     */
    public $sandbox = true;

    /**
     * Business account email (merchant) used for PayPal Standard redirect.
     * Replace with your sandbox or live merchant address.
     */
    public $businessEmail = 'merchant@example.com';

    /**
     * Currency code for payments.
     */
    public $currency = 'EUR';

    /**
     * Relative return and cancel URLs (will be passed to site_url()).
     */
    public $returnUrl = 'reservation/paypal/success';
    public $cancelUrl = 'reservation/paypal/cancel';

    public function getBaseUrl(): string
    {
        return $this->sandbox
            ? 'https://www.sandbox.paypal.com/cgi-bin/webscr'
            : 'https://www.paypal.com/cgi-bin/webscr';
    }
}
