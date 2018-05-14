<?php

namespace BestitKlarnaOrderManagement\Components\Api\Model;

/**
 * Representation of a Klarna line item as an object.
 *
 * @package BestitKlarnaOrderManagement\Components\Api\Model
 *
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
class LineItem
{
    /** @var string */
    public $reference;

    /** @var string */
    public $type;

    /** @var int */
    public $quantity;

    /** @var string */
    public $quantityUnit;

    /** @var string */
    public $name;

    /** @var int */
    public $totalAmount;

    /** @var int */
    public $unitPrice;

    /** @var int */
    public $totalDiscountAmount;

    /** @var int */
    public $taxRate;

    /** @var int */
    public $totalTaxAmount;

    /** @var string|null */
    public $merchantData;

    /** @var string|null */
    public $productUrl;

    /** @var string|null */
    public $imageUrl;

    /** @var ProductIdentifiers */
    public $productIdentifiers;
}
