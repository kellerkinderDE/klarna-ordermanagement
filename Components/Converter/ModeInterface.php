<?php

namespace BestitKlarnaOrderManagement\Components\Converter;

/**
 * Converts shopware basket item mode to Klarna item type
 *
 * Shopware modus are :{$IS_PRODUCT = 0}, {$IS_PREMIUM_PRODUCT = 1}, {$IS_VOUCHER = 2}, {$IS_REBATE = 3}, {$IS_SURCHARGE_DISCOUNT = 4}
 * Klarna types are: physical|discount|shipping_fee|sales_tax|store_credit|gift_card|digital|surcharge
 *
 * @package BestitKlarnaOrderManagement\Components\Converter
 *
 * @author Senan Sharhan <senan.sharhan@bestit-online.de>
 */
interface ModeInterface
{
    /**
     * Is the article mode supported?
     *
     * @param int $mode
     *
     * @return bool
     */
    public function isSupported($mode);

    /**
     * Convert the article mode to Klarna lineItem type
     *
     * @param int   $mode
     * @param float $price
     * @return string
     */
    public function convert($mode, $price = null);
}
