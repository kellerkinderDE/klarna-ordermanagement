<?php

namespace BestitKlarnaOrderManagement\Components\Api\Model;

/**
 * Representation of Klarna shipping info as an object.
 *
 * @package BestitKlarnaOrderManagement\Components\Api\Model
 *
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
class ShippingInfo
{
    /** @var string */
    public $shippingCompany;

    /** @var string|null */
    public $shippingMethod;

    /** @var string|null */
    public $trackingNumber;

    /** @var string|null */
    public $trackingUri;

    /** @var string|null */
    public $returnShippingCompany;

    /** @var string|null */
    public $returnTrackingNumber;

    /** @var string|null */
    public $returnTrackingUri;
}
