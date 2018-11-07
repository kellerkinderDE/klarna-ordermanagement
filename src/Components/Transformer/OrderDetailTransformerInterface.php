<?php

namespace BestitKlarnaOrderManagement\Components\Transformer;

use BestitKlarnaOrderManagement\Components\Api\Model\LineItem;
use Shopware\Models\Order\Detail;

/**
 * Convert Shopware order detail item to Klarna line item.
 *
 * @package BestitKlarnaOrderManagement\Components\Transformer
 *
 * @author  Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
interface OrderDetailTransformerInterface
{
    /**
     * @param Detail $detail
     *
     * @return LineItem
     */
    public function createLineItem(Detail $detail);

    /**
     * @param Detail[] $details
     *
     * @return LineItem[]
     */
    public function createLineItems(array $details);
}
