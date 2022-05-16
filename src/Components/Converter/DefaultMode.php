<?php

namespace BestitKlarnaOrderManagement\Components\Converter;

use BestitKlarnaOrderManagement\Components\Constants;

/**
 * A default converter which return physical as type
 *
 * @author Senan Sharhan <senan.sharhan@bestit-online.de>
 */
class DefaultMode implements ModeInterface
{
    /**
     * Is the article mode supported?
     *
     * @param int $mode
     */
    public function isSupported($mode): bool
    {
        return true;
    }

    /**
     * Convert the article mode to Klarna lineItem type
     *
     * @param int   $mode
     * @param float $price
     */
    public function convert($mode, $price = null): string
    {
        return Constants::KLARNA_LINE_ITEM_TYPE_PHYSICAL;
    }
}
