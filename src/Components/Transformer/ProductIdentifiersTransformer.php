<?php

declare(strict_types=1);

namespace BestitKlarnaOrderManagement\Components\Transformer;

use BestitKlarnaOrderManagement\Components\Api\Model\ProductIdentifiers;

/**
 * Transforms shopware product identifiers to a Klarna model.
 *
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
class ProductIdentifiersTransformer implements ProductIdentifiersTransformerInterface
{
    public function toKlarnaModel(array $lineItem): ProductIdentifiers
    {
        $productIdentifiers = new ProductIdentifiers();

        $breadcrumb         = $lineItem['breadcrumb'] ?? null;
        $ean                = $lineItem['ean'] ?? null;
        $manufacturerNumber = $lineItem['suppliernumber'] ?? null;

        $productIdentifiers->categoryPath           = $breadcrumb ?: null;
        $productIdentifiers->globalTradeItemNumber  = $ean ?: null;
        $productIdentifiers->manufacturerPartNumber = $manufacturerNumber ?: null;

        return $productIdentifiers;
    }
}
