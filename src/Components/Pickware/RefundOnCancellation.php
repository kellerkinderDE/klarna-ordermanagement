<?php

namespace BestitKlarnaOrderManagement\Components\Pickware;

use BestitKlarnaOrderManagement\Components\Api\Model\LineItem;
use BestitKlarnaOrderManagement\Components\Calculator\CalculatorInterface;
use BestitKlarnaOrderManagement\Components\Constants;
use BestitKlarnaOrderManagement\Components\Exception\NoOrderFoundException;
use BestitKlarnaOrderManagement\Components\Facade\Order as OrderFacade;
use BestitKlarnaOrderManagement\Components\Facade\Refund as RefundFacade;
use BestitKlarnaOrderManagement\Components\Transformer\OrderDetailTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Shopware\Models\Order\Detail;
use Shopware\Models\Order\Detail as OrderDetail;
use Shopware\Models\Order\Order;

/**
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 * @author Senan Sharhan <senan.sharhan@bestit-online.de>
 */
class RefundOnCancellation
{
    /** @var null|float */
    protected $shippingCosts;
    /** @var null|float */
    protected $shippingCostsNet;
    /** @var null|float */
    protected $shippingCostsTax;
    /** @var EntityManagerInterface */
    protected $em;
    /** @var OrderDetailTransformer */
    protected $orderDetailTransformer;
    /** @var OrderFacade */
    protected $orderFacade;
    /** @var CalculatorInterface */
    protected $calculator;
    /** @var RefundFacade */
    protected $refundFacade;

    public function __construct(
        EntityManagerInterface $em,
        OrderDetailTransformer $orderDetailTransformer,
        OrderFacade $orderFacade,
        CalculatorInterface $calculator,
        RefundFacade $refundFacade
    ) {
        $this->em                     = $em;
        $this->orderDetailTransformer = $orderDetailTransformer;
        $this->orderFacade            = $orderFacade;
        $this->calculator             = $calculator;
        $this->refundFacade           = $refundFacade;
    }

    /**
     * @param $shippingCosts
     * @param $shippingCostsNet
     */
    public function setShippingCosts($shippingCosts, $shippingCostsNet): void
    {
        $this->shippingCosts    = $shippingCosts;
        $this->shippingCostsNet = $shippingCostsNet;
    }

    /**
     * @param $orderId
     */
    public function refundFor($orderId, array $cancelledItems): void
    {
        $order = $this->em->find(Order::class, $orderId);

        $cancelledItemsAsOrderDetails    = $this->transformPickwareItemsToShopwareDetails($cancelledItems);
        $cancelledItemsAsKlarnaLineItems = $this->transformToKlarnaLineItems($cancelledItemsAsOrderDetails);

        $klarnaOrderId = $order->getTransactionId();

        if (empty($klarnaOrderId)) {
            return;
        }

        if ($this->shippingCosts !== null) {
            $cancelledItemsAsKlarnaLineItems = $this->appendShippingCostsLineItem(
                $klarnaOrderId,
                $cancelledItemsAsKlarnaLineItems
            );
        }

        $this->refundFacade->create(
            $klarnaOrderId,
            $this->calculateRefundAmount($cancelledItemsAsOrderDetails),
            $cancelledItemsAsKlarnaLineItems
        );
    }

    protected function transformPickwareItemsToShopwareDetails(array $cancelledItems): array
    {
        return array_map(function ($item) {
            $detail = $this->em->find(OrderDetail::class, $item['id']);

            // Set the quantity to the returned quantity.
            $detail->setQuantity($item['quantity']);

            // Make sure we do not update the quantity by accident.
            $this->em->detach($detail);

            return $detail;
        }, $cancelledItems);
    }

    /**
     * @param Detail[] $cancelledItems
     *
     * @return LineItem[]
     */
    protected function transformToKlarnaLineItems(array $cancelledItems): array
    {
        return $this->orderDetailTransformer->createLineItems($cancelledItems);
    }

    /**
     * @param string     $orderTransactionId
     * @param LineItem[] $lineItems
     *
     * @throws NoOrderFoundException
     *
     * @return LineItem[]
     */
    protected function appendShippingCostsLineItem($orderTransactionId, array $lineItems): array
    {
        $response = $this->orderFacade->get($orderTransactionId);

        if ($response->isError()) {
            throw new NoOrderFoundException("Order {$orderTransactionId} can not be found");
        }

        /** @var \BestitKlarnaOrderManagement\Components\Api\Model\Order $klarnaOrder */
        $klarnaOrder = $response->getResponseObject();

        foreach ($klarnaOrder->orderLines as $orderLine) {
            if ($orderLine->reference === Constants::SHIPPING_COSTS_REFERENCE) {
                $lineItems[] = $orderLine;
            }
        }

        return $lineItems;
    }

    /**
     * @param OrderDetail[] $orderDetails
     */
    protected function calculateRefundAmount(array $orderDetails): int
    {
        $refundAmount = 0.00;

        foreach ($orderDetails as $orderDetail) {
            $refundAmount += ($orderDetail->getPrice() * $orderDetail->getQuantity());
        }

        if ($this->shippingCosts > 0) {
            $refundAmount += $this->shippingCosts;
        }

        return $this->calculator->toCents($refundAmount);
    }
}
