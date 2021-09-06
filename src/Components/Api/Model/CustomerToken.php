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

    /** @var string|null */
    public $description;

    /** @var string|null */
    public $intendedUse;

    /** @var array|null */
    public $merchantUrls;
}
