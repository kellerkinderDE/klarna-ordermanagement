<?php

declare(strict_types=1);

namespace BestitKlarnaOrderManagement\Components\Api\Model;

use DateTime;

/**
 * Representation of a Klarna capture as an object.
 *
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
class Capture
{
    /** @var string */
    public $captureId;

    /** @var string */
    public $klarnaReference;

    /** @var int */
    public $capturedAmount;

    /** @var DateTime */
    public $capturedAt;

    /** @var null|string */
    public $description;

    /** @var LineItem[] */
    public $orderLines;

    /** @var int */
    public $refundedAmount;

    /** @var BillingAddress */
    public $billingAddress;

    /** @var ShippingAddress */
    public $shippingAddress;

    /** @var ShippingInfo[] */
    public $shippingInfo;
}
