<?php

declare(strict_types=1);

namespace BestitKlarnaOrderManagement\Components\Api\Model;

class CustomerToken
{
    /** @var null|string */
    public $purchaseCountry;

    /** @var null|string */
    public $purchaseCurrency;

    /** @var null|string */
    public $locale;

    /** @var null|Customer */
    public $customer;

    /** @var BillingAddress */
    public $billingAddress;

    /** @var string */
    public $description = 'AboCommerce Order';

    /** @var string */
    public $intendedUse = 'SUBSCRIPTION';

    /** @var null|array */
    public $merchantUrls;
}
