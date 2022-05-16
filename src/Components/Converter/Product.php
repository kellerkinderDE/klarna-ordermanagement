<?php

declare(strict_types=1);

namespace BestitKlarnaOrderManagement\Components\Converter;

use BestitKlarnaOrderManagement\Components\Constants;
use BestitKlarnaOrderManagement\Components\Exception\NoSupportedModeException;

/**
 * Convert Shopware Product mode to physical
 *
 * @author Senan Sharhan <senan.sharhan@bestit-online.de>
 */
class Product implements ModeInterface
{
    /**
     * Is the article mode supported?
     *
     * @param int $mode
     */
    public function isSupported($mode): bool
    {
        return $mode === Constants::SHOPWARE_PRODUCT_MODE;
    }

    /**
     * Convert the article mode to Klarna lineItem type
     *
     * @param int   $mode
     * @param float $price
     *
     * @throws NoSupportedModeException
     */
    public function convert($mode, $price = null): string
    {
        if (!$this->isSupported($mode)) {
            throw new NoSupportedModeException();
        }

        return Constants::KLARNA_LINE_ITEM_TYPE_PHYSICAL;
    }
}
