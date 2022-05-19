<?php

namespace BestitKlarnaOrderManagement\Components\Transformer;

use BestitKlarnaOrderManagement\Components\Api\Model\LineItem;
use Shopware\Models\Order\Detail;

/**
 * Convert Shopware order detail item to Klarna line item.
 *
 * @author  Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
interface OrderDetailTransformerInterface
{
    public function createLineItem(Detail $detail): LineItem;

    /**
     * @param Detail[] $details
     *
     * @return LineItem[]
     */
    public function createLineItems(array $details): array;
}
