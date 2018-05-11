<?php

namespace BestitKlarnaOrderManagement\Components\Converter;

use BestitKlarnaOrderManagement\Components\Constants;
use BestitKlarnaOrderManagement\Components\Exception\NoSupportedModeException;

/**
 * Convert Shopware Premium Product mode to physical
 *
 * @package BestitKlarnaOrderManagement\Components\Converter
 *
 * @author Senan Sharhan <senan.sharhan@bestit-online.de>
 */
class PremiumProduct implements ModeInterface
{
    /**
     * Is the article mode supported?
     *
     * @param int $mode
     *
     * @return bool
     */
    public function isSupported($mode)
    {
        return $mode === Constants::SHOPWARE_PREMIUM_PRODUCT_MODE;
    }

    /**
     * Convert the article mode to Klarna lineItem type
     *
     * @param int   $mode
     * @param float $price
     *
     * @return string
     *
     * @throws NoSupportedModeException
     */
    public function convert($mode, $price = null)
    {
        if (!$this->isSupported($mode)) {
            throw new NoSupportedModeException();
        }

        return Constants::KLARNA_LINE_ITEM_TYPE_PHYSICAL;
    }
}
