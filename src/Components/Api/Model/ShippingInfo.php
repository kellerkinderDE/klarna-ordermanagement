<?php

namespace BestitKlarnaOrderManagement\Components\Api\Model;

/**
 * Representation of Klarna shipping info as an object.
 *
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
class ShippingInfo
{
    /** @var null|string */
    public $shippingCompany;

    /** @var null|string */
    public $shippingMethod;

    /** @var null|string */
    public $trackingNumber;

    /** @var null|string */
    public $trackingUri;

    /** @var null|string */
    public $returnShippingCompany;

    /** @var null|string */
    public $returnTrackingNumber;

    /** @var null|string */
    public $returnTrackingUri;
}
