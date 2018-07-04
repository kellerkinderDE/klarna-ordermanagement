<?php

namespace BestitKlarnaOrderManagement\Components\Transformer;

use BestitKlarnaOrderManagement\Components\Api\Model\ProductIdentifiers;

/**
 * Transforms shopware product identifiers to a Klarna model.
 *
 * @package BestitKlarnaOrderManagement\Components\Transformer
 *
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
class ProductIdentifiersTransformer implements ProductIdentifiersTransformerInterface
{
    /**
     * @param array $lineItem
     *
     * @return ProductIdentifiers
     */
    public function toKlarnaModel(array $lineItem)
    {
        $productIdentifiers = new ProductIdentifiers();

        $breadcrumb = isset($lineItem['breadcrumb']) ? $lineItem['breadcrumb'] : null;
        $ean = isset($lineItem['ean']) ? $lineItem['ean'] : null;
        $manufacturerNumber = isset($lineItem['suppliernumber']) ? $lineItem['suppliernumber'] : null;

        $productIdentifiers->categoryPath = $breadcrumb ?: null;
        $productIdentifiers->globalTradeItemNumber = $ean ?: null;
        $productIdentifiers->manufacturerPartNumber = $manufacturerNumber ?: null;

        return $productIdentifiers;
    }
}
