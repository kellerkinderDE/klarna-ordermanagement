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
use Shopware\Models\Order\Detail as OrderDetail;
use Shopware\Models\Order\Detail;
use Shopware\Models\Order\Order;

/**
 * @package BestitKlarnaOrderManagement\Components\Pickware
 *
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 * @author Senan Sharhan <senan.sharhan@bestit-online.de>
 */
class RefundOnCancellation
{
    /** @var float|null */
    protected $shippingCosts;
    /** @var float|null */
    protected $shippingCostsNet;
    /** @var float|null */
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

    /**
     * @param EntityManagerInterface $em
     * @param OrderDetailTransformer $orderDetailTransformer
     * @param OrderFacade $orderFacade
     * @param CalculatorInterface $calculator
     * @param RefundFacade $refundFacade
     */
    public function __construct(
        EntityManagerInterface $em,
        OrderDetailTransformer $orderDetailTransformer,
        OrderFacade $orderFacade,
        CalculatorInterface $calculator,
        RefundFacade $refundFacade
    ) {
        $this->em = $em;
        $this->orderDetailTransformer = $orderDetailTransformer;
        $this->orderFacade = $orderFacade;
        $this->calculator = $calculator;
        $this->refundFacade = $refundFacade;
    }

    /**
     * @param $shippingCosts
     * @param $shippingCostsNet
     *
     * @return void
     */
    public function setShippingCosts($shippingCosts, $shippingCostsNet)
    {
        $this->shippingCosts = $shippingCosts;
        $this->shippingCostsNet = $shippingCostsNet;
    }

    /**
     * @param $orderId
     * @param array $cancelledItems
     *
     * @return void
     */
    public function refundFor($orderId, array $cancelledItems)
    {
        $order = $this->em->find(Order::class, $orderId);

        $cancelledItemsAsOrderDetails = $this->transformPickwareItemsToShopwareDetails($cancelledItems);
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

    /**
     * @param array $cancelledItems
     *
     * @return array
     */
    protected function transformPickwareItemsToShopwareDetails(array $cancelledItems)
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
    protected function transformToKlarnaLineItems(array $cancelledItems)
    {
        return $this->orderDetailTransformer->createLineItems($cancelledItems);
    }

    /**
     * @param string $orderTransactionId
     * @param LineItem[] $lineItems
     *
     * @return LineItem[]
     *
     * @throws NoOrderFoundException
     */
    protected function appendShippingCostsLineItem($orderTransactionId, array $lineItems)
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
     *
     * @return int
     */
    protected function calculateRefundAmount(array $orderDetails)
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
