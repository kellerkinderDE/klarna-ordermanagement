<?php

declare(strict_types=1);

namespace BestitKlarnaOrderManagement\Components\Api\Model;

/**
 * Representation of a Klarna line item as an object.
 *
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
class LineItem
{
    /**
     * Since custom product plugin order line items (values) dont have an
     * article ordernumber, we have to allow null
     *
     * @var null|string
     */
    public $reference;

    /** @var string */
    public $type;

    /** @var int */
    public $quantity;

    /** @var null|string */
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

    /** @var null|string */
    public $merchantData;

    /** @var null|string */
    public $productUrl;

    /** @var null|string */
    public $imageUrl;

    /** @var ProductIdentifiers */
    public $productIdentifiers;
}
