<?php

namespace BestitKlarnaOrderManagement\Components\DataFormatter;

/**
 * Builds the complete product URLs for line items (by default it is a "shopware.php" URL).
 *
 * @package BestitKlarnaOrderManagement\Components\DataFormatter
 *
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
interface ProductUrlBuilderInterface
{
    /**
     * @param array $lineItems  In the format that `Shopware_Controllers_Frontend_Checkout::getBasket()['content']`
     *                          returns it.
     *
     * @return array
     */
    public function buildProductUrls(array $lineItems);
}
