<?php

declare(strict_types=1);

namespace BestitKlarnaOrderManagement\Components\Api\Model;

class RecurringOrder
{
    /** @var string|null */
    public $merchantReference1 = 'lorem';

    /** @var string|null */
    public $merchantData = 'test';

    /** @var string|null */
    public $locale;

    /** @var bool */
    public $autoCapture = false;

    /** @var string|null */
    public $purchaseCurrency;

    /** @var int|null */
    public $orderAmount;

    /** @var int|null */
    public $orderTaxAmount;

    /** @var LineItem[] */
    public $orderLines;
}