<?php

namespace BestitKlarnaOrderManagement\Components\Shared;

use Shopware\Models\Shop\Shop;

/**
 * Builds the locale in the format that Klarna needs.
 *
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
class Localizer
{
    /** @var Shop */
    protected $shop;

    public function __construct(Shop $shop)
    {
        $this->shop = $shop;
    }

    public function localize(): string
    {
        /**
         * Shopware uses "de_DE" as the format for locales whereas the normal way is "de-DE" which
         * Klarna required, so we just replace "_" with "-".
         */
        return str_replace('_', '-', $this->shop->getLocale()->getLocale());
    }
}
