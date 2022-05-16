<?php

declare(strict_types=1);

namespace BestitKlarnaOrderManagement\Components\Api\Model;

/**
 * Representation of a Klarna merchant fees as an object.
 *
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
class MerchantFees
{
    /** @var int */
    public $fixedAmount;

    /** @var int */
    public $commissionRate;
}
