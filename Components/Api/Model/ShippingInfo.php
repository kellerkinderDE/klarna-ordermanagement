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

    /** @var string */
    public $shippingMethod;

    /** @var string */
    public $trackingNumber;

    /** @var string */
    public $trackingUri;

    /** @var string */
    public $returnShippingCompany;

    /** @var string */
    public $returnTrackingNumber;

    /** @var string */
    public $returnTrackingUri;
}
