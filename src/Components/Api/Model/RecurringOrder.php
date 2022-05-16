<?php

namespace BestitKlarnaOrderManagement\Components\Api\Model;

class RecurringOrder
{
    /** @var null|string */
    public $merchantReference1;

    /** @var null|string */
    public $merchantData;

    /** @var null|string */
    public $locale;

    /** @var bool */
    public $autoCapture = false;

    /** @var null|string */
    public $purchaseCurrency;

    /** @var null|int */
    public $orderAmount;

    /** @var null|int */
    public $orderTaxAmount;

    /** @var LineItem[] */
    public $orderLines;

    /** @var null|ShippingAddress */
    public $shippingAddress;
}
