<?php

declare(strict_types=1);

namespace BestitKlarnaOrderManagement\Components\Pickware;

use BestitKlarnaOrderManagement\Components\Api\Model\LineItem;
use BestitKlarnaOrderManagement\Components\Calculator\CalculatorInterface;
use BestitKlarnaOrderManagement\Components\ConfigReader;
use BestitKlarnaOrderManagement\Components\Facade\Capture as CaptureFacade;
use BestitKlarnaOrderManagement\Components\Facade\Order as OrderFacade;
use BestitKlarnaOrderManagement\Components\Facade\Refund as RefundFacade;
use BestitKlarnaOrderManagement\Components\Transformer\OrderDetailTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Shopware\Models\Order\Detail;
use Shopware\Models\Order\Detail as OrderDetail;

/**
 * In this class we trigger the change to the “shipped” field in the positions tab,
 * in order to make automatically capture or refund.
 *
 * @author Senan Sharhan <senan.sharhan@bestit-online.de>
 */
class CaptureOnShipped
{
    /** @var EntityManagerInterface */
    protected $em;
    /** @var OrderDetailTransformer */
    protected $orderDetailTransformer;
    /** @var OrderFacade */
    protected $orderFacade;
    /** @var CalculatorInterface */
    protected $calculator;
    /** @var CaptureFacade */
    protected $captureFacade;
    /** @var RefundFacade */
    protected $refundFacade;
    /** @var ConfigReader */
    protected $configReader;
    /** @var array */
    protected $shippedBackup = [];

    public function __construct(
        EntityManagerInterface $em,
        OrderDetailTransformer $orderDetailTransformer,
        OrderFacade $orderFacade,
        CalculatorInterface $calculator,
        CaptureFacade $captureFacade,
        RefundFacade $refundFacade,
        ConfigReader $configReader
    ) {
        $this->em                     = $em;
        $this->orderDetailTransformer = $orderDetailTransformer;
        $this->orderFacade            = $orderFacade;
        $this->calculator             = $calculator;
        $this->captureFacade          = $captureFacade;
        $this->refundFacade           = $refundFacade;
        $this->configReader           = $configReader;
    }

    /**
     * The old shipped value is saved in order to be able to compare with the new value and find the difference.
     *
     * @param string $ItemId
     */
    public function saveOldShippedValue($ItemId): void
    {
        $orderDetail = $this->em->find(OrderDetail::class, $ItemId);

        if ($orderDetail !== null) {
            $this->shippedBackup = [
                $orderDetail->getId() => $orderDetail->getShipped(),
            ];
        }
    }

    /**
     * Here we calculate the quantity of the shipped item, and automatically capture/refund the amount.
     *
     * @param string $ItemId
     */
    public function captureIfShipped($ItemId): void
    {
        /** @var OrderDetail $orderDetail */
        $orderDetail = $this->em->find(OrderDetail::class, $ItemId);

        if ($orderDetail === null || !isset($this->shippedBackup[$orderDetail->getId()])) {
            return;
        }

        $shippedValue       = $orderDetail->getShipped() - $this->shippedBackup[$orderDetail->getId()];
        $order              = $orderDetail->getOrder();
        $orderTransactionId = $order->getTransactionId();

        if (empty($orderTransactionId)) {
            return;
        }

        if ($shippedValue > 0) {
            $this->captureItem($orderTransactionId, $orderDetail, $shippedValue);
        } elseif ($shippedValue < 0 && $this->refundEnabled()) {
            $this->refundItem($orderTransactionId, $orderDetail, $shippedValue);
        }

        // Make sure we do not update the quantity by accident.
        $this->em->detach($orderDetail);
    }

    /**
     * @param string      $orderTransactionId
     * @param OrderDetail $orderDetail
     * @param int         $shippedValue
     */
    public function captureItem($orderTransactionId, $orderDetail, $shippedValue): void
    {
        // Set the quantity to the captured quantity.
        $orderDetail->setQuantity($shippedValue);
        $cancelledItemsAsKlarnaLineItems = $this->transformToKlarnaLineItems([$orderDetail]);

        $this->captureFacade->create(
            $orderTransactionId,
            $this->calculateCaptureAmount($orderDetail, $shippedValue),
            $cancelledItemsAsKlarnaLineItems
        );
    }

    /**
     * @param string      $orderTransactionId
     * @param OrderDetail $orderDetail
     * @param int         $shippedValue
     */
    public function refundItem($orderTransactionId, $orderDetail, $shippedValue): void
    {
        // For refund the difference is negative number, so we are multiplying it with -1
        $quantityToBeRefunded = $shippedValue * -1;

        // Set the quantity to the refunded quantity.
        $orderDetail->setQuantity($quantityToBeRefunded);
        $cancelledItemsAsKlarnaLineItems = $this->transformToKlarnaLineItems([$orderDetail]);

        $this->refundFacade->create(
            $orderTransactionId,
            $this->calculateCaptureAmount($orderDetail, $quantityToBeRefunded),
            $cancelledItemsAsKlarnaLineItems
        );
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
     * @param $orderDetail
     * @param $quantity
     */
    protected function calculateCaptureAmount($orderDetail, $quantity): int
    {
        return $this->calculator->toCents($orderDetail->getPrice() * $quantity);
    }

    /**
     * If a refund should be done by reducing the shipped value, true will be returned.
     */
    protected function refundEnabled(): bool
    {
        $refundEnabled = (int) $this->configReader->get('pickware_refund_enabled', 0);

        return $refundEnabled === 1;
    }
}
