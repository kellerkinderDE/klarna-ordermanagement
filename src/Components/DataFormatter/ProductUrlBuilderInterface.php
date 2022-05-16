<?php

namespace BestitKlarnaOrderManagement\Components\DataFormatter;

/**
 * Builds the complete product URLs for line items (by default it is a "shopware.php" URL).
 *
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
interface ProductUrlBuilderInterface
{
    /**
     * @param array $lineItems in the format that `Shopware_Controllers_Frontend_Checkout::getBasket()['content']`
     *                         returns it
     */
    public function addProductUrls(array $lineItems): array;
}
