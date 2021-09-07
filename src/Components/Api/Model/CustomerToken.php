<?php

declare(strict_types=1);

namespace BestitKlarnaOrderManagement\Components\Api\Model;

class CustomerToken
{
    /** @var string|null */
    public $purchaseCountry;

    /** @var string|null */
    public $purchaseCurrency;

    /** @var string|null */
    public $locale;

    /** @var Customer|null */
    public $customer;

    /** @var BillingAddress */
    public $billingAddress;

    /** @var string */
    public $description = 'AboCommerce Order';

    /** @var string */
    public $intendedUse = 'SUBSCRIPTION';

    /** @var array|null */
    public $merchantUrls;
}
