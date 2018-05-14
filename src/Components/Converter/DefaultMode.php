<?php

namespace BestitKlarnaOrderManagement\Components\Converter;

use BestitKlarnaOrderManagement\Components\Constants;

/**
 * A default converter which return physical as type
 *
 * @package BestitKlarnaOrderManagement\Components\Converter
 *
 * @author Senan Sharhan <senan.sharhan@bestit-online.de>
 */
class DefaultMode implements ModeInterface
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
        return true;
    }

    /**
     * Convert the article mode to Klarna lineItem type
     *
     * @param int   $mode
     * @param float $price
     *
     * @return string
     */
    public function convert($mode, $price = null)
    {
        return Constants::KLARNA_LINE_ITEM_TYPE_PHYSICAL;
    }
}
