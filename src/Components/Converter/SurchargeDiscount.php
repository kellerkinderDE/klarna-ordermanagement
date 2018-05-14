<?php

namespace BestitKlarnaOrderManagement\Components\Converter;

use BestitKlarnaOrderManagement\Components\Constants;
use BestitKlarnaOrderManagement\Components\Exception\NoSupportedModeException;

/**
 * Converts Shopware Surcharge/Discount mode to surcharge/discount
 *
 * @package BestitKlarnaOrderManagement\Components\Converter
 *
 * @author Senan Sharhan <senan.sharhan@bestit-online.de>
 */
class SurchargeDiscount implements ModeInterface
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
        return $mode === Constants::SHOPWARE_SURCHARGE_DISCOUNT_MODE;
    }

    /**
     * Shopware use the mode 4 for a surcharge and discount, and for this reason we check if the price is
     * to find out what is the correct item type surcharge or discount.
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

        return $price > 0 ? Constants::KLARNA_LINE_ITEM_TYPE_SURCHARGE : Constants::KLARNA_LINE_ITEM_TYPE_DISCOUNT;
    }
}
