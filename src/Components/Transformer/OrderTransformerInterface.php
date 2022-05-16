<?php

namespace BestitKlarnaOrderManagement\Components\Transformer;

use BestitKlarnaOrderManagement\Components\Api\Model\BillingAddress;
use BestitKlarnaOrderManagement\Components\Api\Model\LineItem;
use BestitKlarnaOrderManagement\Components\Api\Model\ShippingAddress;
use Shopware\Models\Order\Billing as SwOrderBillingModel;
use Shopware\Models\Order\Shipping as SwOrderShippingModel;

/**
 * Convert Shopware Order models to klarna models
 *
 * @author  Senan Sharhan <senan.sharhan@bestit-online.de>
 */
interface OrderTransformerInterface
{
    public function createShippingAddress(SwOrderShippingModel $shipping): ShippingAddress;

    public function createBillingAddress(SwOrderBillingModel $billing): BillingAddress;

    /**
     * @return LineItem[]
     */
    public function createLineItems(array $details): array;

    /**
     * @param float $orderAmount
     */
    public function createOrderAmount($orderAmount): int;
}
