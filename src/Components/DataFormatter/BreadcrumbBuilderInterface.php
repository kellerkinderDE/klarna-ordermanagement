<?php

namespace BestitKlarnaOrderManagement\Components\DataFormatter;

use Shopware\Bundle\StoreFrontBundle\Struct\Category;

/**
 * Builds a category breadcrumb for the given line items.
 *
 * @package BestitKlarnaOrderManagement\Components\DataFormatter
 *
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
interface BreadcrumbBuilderInterface
{
    /**
     * Builds a breadcrumb for each line item and sets it as `$lineItems['breadcrumb']`.
     *
     * @param array $lineItems  In the format that `Shopware_Controllers_Frontend_Checkout::getBasket()['content']`
     *                          returns it.
     *
     * @return array
     */
    public function buildForLineItems(array $lineItems);

    /**
     * Builds a breadcrumb for the given categories.
     *
     * @param Category[] $categories
     *
     * @return string
     *
     * @internal This should not be used from the outside. It's only declared here so it can be easily decorated
     *           if need be.
     */
    public function buildBreadcrumb(array $categories);
}
