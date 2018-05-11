<?php

namespace BestitKlarnaOrderManagement\Components\Api\Model;

use DateTime;

/**
 * Representation of a Klarna refund as an object.
 *
 * @package BestitKlarnaOrderManagement\Components\Api\Model
 *
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
class Refund
{
    /** @var int */
    public $refundedAmount;

    /** @var DateTime */
    public $refundedAt;

    /** @var string */
    public $description;

    /** @var LineItem[] */
    public $orderLines;
}
