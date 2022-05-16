<?php

namespace BestitKlarnaOrderManagement\Components\Api\Model;

/**
 * Representation of Klarna product identifiers as an object.
 *
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
class ProductIdentifiers
{
    /** @var null|string */
    public $categoryPath;

    /** @var null|string */
    public $globalTradeItemNumber;

    /** @var null|string */
    public $manufacturerPartNumber;

    /** @var null|string */
    public $brand;

    public function hasData(): bool
    {
        return !empty($this->categoryPath) || !empty($this->globalTradeItemNumber) || !empty($this->manufacturerPartNumber) || !empty($this->brand);
    }
}
