<?php

namespace BestitKlarnaOrderManagement\Components\Api\Model;

/**
 * Representation of Klarna product identifiers as an object.
 *
 * @package BestitKlarnaOrderManagement\Components\Api\Model
 *
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
class ProductIdentifiers
{
    /** @var string|null */
    public $categoryPath;

    /** @var string|null */
    public $globalTradeItemNumber;

    /** @var string|null */
    public $manufacturerPartNumber;

    /** @var string|null */
    public $brand;
}
