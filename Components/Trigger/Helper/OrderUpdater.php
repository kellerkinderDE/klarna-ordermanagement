<?php

namespace BestitKlarnaOrderManagement\Components\Trigger\Helper;

use BestitKlarnaOrderManagement\Components\Api\Model\LineItem;
use BestitKlarnaOrderManagement\Components\Api\Model\Order as KlarnaOrder;
use BestitKlarnaOrderManagement\Components\Api\Response;
use BestitKlarnaOrderManagement\Components\Constants;
use BestitKlarnaOrderManagement\Components\DataProvider\DataProvider;
use BestitKlarnaOrderManagement\Components\Facade\Order as OrderFacade;
use BestitKlarnaOrderManagement\Components\Transformer\OrderTransformer;

/**
 * Updates the order with the given line items.
 *
 * @package BestitKlarnaOrderManagement\Components\Trigger\Helper
 *
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 * @author Senan Sharhan <senan.sharhan@bestit-online.de>
 */
class OrderUpdater
{
    /** @var OrderFacade */
    protected $orderFacade;
    /** @var OrderTransformer */
    protected $orderTransformer;
    /** @var DataProvider */
    protected $dataProvider;

    /**
     * @param OrderFacade      $orderFacade
     * @param OrderTransformer $orderTransformer
     * @param DataProvider     $dataProvider
     */
    public function __construct(
        OrderFacade $orderFacade,
        OrderTransformer $orderTransformer,
        DataProvider $dataProvider
    ) {
        $this->orderFacade = $orderFacade;
        $this->orderTransformer = $orderTransformer;
        $this->dataProvider = $dataProvider;
    }

    /**
     * @param int   $orderId
     * @param array $orderDetails
     *
     * @return Response
     */
    public function execute($orderId, array $orderDetails)
    {
        $swOrder = $this->dataProvider->getSwOrder($orderId);

        $shippingCosts = $swOrder->getInvoiceShipping() ?: 0;
        $klarnaOrderId = $swOrder->getTransactionId();

        $lineItems = $this->orderTransformer->createLineItems($orderDetails);

        if ($this->shippingCostsExist($shippingCosts)) {
            $response = $this->orderFacade->get($klarnaOrderId);

            if ($response->isError()) {
                return $response;
            }

            $shippingLineItem = $this->getShippingCostLineItem($response->getResponseObject());

            if ($shippingLineItem !== null) {
                $lineItems[] = $shippingLineItem;
            }
        }

        return $this->orderFacade->updateOrder($klarnaOrderId, $this->calculateTotalAmount($lineItems), $lineItems);
    }

    /**
     * @param float $shippingCosts
     *
     * @return bool
     */
    protected function shippingCostsExist($shippingCosts)
    {
        return $shippingCosts >= 0;
    }

    protected function getShippingCostLineItem(KlarnaOrder $klarnaOrder)
    {
        foreach ($klarnaOrder->orderLines as $lineItem) {
            if ($lineItem->reference === Constants::SHIPPING_COSTS_REFERENCE) {
                return $lineItem;
            }
        }

        return null;
    }

    /**
     * @param LineItem[] $lineItems
     *
     * @return float
     */
    protected function calculateTotalAmount(array $lineItems)
    {
        $totalAmount = 0.00;

        foreach ($lineItems as $lineItem) {
            $totalAmount += $lineItem->totalAmount;
        }

        return $totalAmount;
    }
}
