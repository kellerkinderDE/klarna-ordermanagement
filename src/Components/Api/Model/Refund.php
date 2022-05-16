<?php

declare(strict_types=1);

namespace BestitKlarnaOrderManagement\Components\Api\Model;

use DateTime;

/**
 * Representation of a Klarna refund as an object.
 *
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
class Refund
{
    /** @var int */
    public $refundedAmount;

    /** @var DateTime */
    public $refundedAt;

    /** @var null|string */
    public $description;

    /** @var LineItem[] */
    public $orderLines;
}
