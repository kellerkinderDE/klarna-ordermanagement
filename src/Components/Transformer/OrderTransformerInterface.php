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
 * @package BestitKlarnaOrderManagement\Components\Transformer
 *
 * @author  Senan Sharhan <senan.sharhan@bestit-online.de>
 */
interface OrderTransformerInterface
{
    /**
     * @param SwOrderShippingModel $shipping
     *
     * @return ShippingAddress
     */
    public function createShippingAddress(SwOrderShippingModel $shipping);

    /**
     * @param SwOrderBillingModel $billing
     *
     * @return BillingAddress
     */
    public function createBillingAddress(SwOrderBillingModel $billing);

    /**
     * @param array $details
     *
     * @return LineItem[]
     */
    public function createLineItems(array $details);

    /**
     * @param float $orderAmount
     *
     * @return int
     */
    public function createOrderAmount($orderAmount);
}
