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
interface ProductIdentifiersTransformerInterface
{
    /**
     * @param array $lineItem
     *
     * @return ProductIdentifiers
     */
    public function toKlarnaModel(array $lineItem);
}
