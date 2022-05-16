<?php

namespace BestitKlarnaOrderManagement\Components\Transformer;

use BestitKlarnaOrderManagement\Components\Api\Model\LineItem;

/**
 * Transforms shopware line items to Klarna models.
 *
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
interface LineItemTransformerInterface
{
    /**
     * Transforms a list of shopware basket items to a list of klarna line items.
     *
     * @param array $lineItems in the format that `Shopware_Controllers_Frontend_Checkout::getBasket()['content']`
     *                         returns it
     *
     * @return LineItem[]
     */
    public function toKlarnaModelList(array $lineItems): array;

    /**
     * Transforms a shopware basket item to a klarna line item.
     *
     * @internal This should not be used from the outside. It's only declared here so it can be easily decorated
     *           if need be.
     */
    public function toKlarnaModel(array $lineItem): LineItem;

    /**
     * Specifies that the list should include the given shipping costs as a line item.
     *
     * @param float $shippingCostsWithTax
     * @param float $shippingCostsNet
     * @param float $shippingCostsTaxRate
     * @param array $shippingcostsTaxProportional
     */
    public function withShippingCosts($shippingCostsWithTax, $shippingCostsNet, $shippingCostsTaxRate, $shippingcostsTaxProportional = null): LineItemTransformerInterface;
}
