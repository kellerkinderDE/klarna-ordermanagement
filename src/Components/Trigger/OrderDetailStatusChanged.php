<?php

declare(strict_types=1);

namespace BestitKlarnaOrderManagement\Components\Trigger;

use BestitKlarnaOrderManagement\Components\Api\Model\Order as KlarnaOrderModel;
use BestitKlarnaOrderManagement\Components\ConfigReader;
use BestitKlarnaOrderManagement\Components\Facade\Order as OrderFacade;
use BestitKlarnaOrderManagement\Components\PaymentInsights;
use BestitKlarnaOrderManagement\Components\Storage\DataProvider;
use BestitKlarnaOrderManagement\Components\Trigger\Action\ActionFactoryInterface;
use Shopware\Models\Order\Detail as SwOrderDetail;
use Shopware\Models\Order\Order as SwOrderModel;
use Shopware\Models\Order\Status;

/**
 * Executes any defined triggers when the status of an order detail item is changed.
 *
 * @author  Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
class OrderDetailStatusChanged
{
    /** @var OrderFacade */
    protected $orderFacade;
    /** @var ConfigReader */
    protected $configReader;
    /** @var PaymentInsights */
    protected $paymentInsights;
    /** @var DataProvider */
    protected $dataProvider;
    /** @var ActionFactoryInterface */
    protected $actionFactory;

    public function __construct(
        OrderFacade $orderFacade,
        ConfigReader $configReader,
        PaymentInsights $paymentInsights,
        DataProvider $dataProvider,
        ActionFactoryInterface $actionFactory
    ) {
        $this->orderFacade     = $orderFacade;
        $this->configReader    = $configReader;
        $this->paymentInsights = $paymentInsights;
        $this->dataProvider    = $dataProvider;
        $this->actionFactory   = $actionFactory;
    }

    public function executeDefinedTriggers(SwOrderDetail $orderDetail): SwOrderModel
    {
        $swOrder = $orderDetail->getOrder();

        if ($this->automaticTriggersAreDisabled() || $this->isNotAKlarnaOrder($swOrder)) {
            return $swOrder;
        }

        $klarnaOrderId = $swOrder->getTransactionId();
        $statusId      = $orderDetail->getStatus()->getId();

        $action = $this->actionFactory->createForDetailStatus($statusId);

        if ($action === null) {
            return $swOrder;
        }

        $klarnaOrderResponse = $this->orderFacade->get($klarnaOrderId);

        if ($klarnaOrderResponse->isError()) {
            return $this->setPaymentStatus($swOrder, Status::PAYMENT_STATE_REVIEW_NECESSARY);
        }

        /** @var KlarnaOrderModel $klarnaOrder */
        $klarnaOrder = $klarnaOrderResponse->getResponseObject();

        $paymentStatus = $action->trigger($swOrder, $klarnaOrder, $orderDetail);

        if ($paymentStatus === null) {
            return $swOrder;
        }

        return $this->setPaymentStatus($swOrder, $paymentStatus);
    }

    /**
     * @param int $statusId
     */
    protected function setPaymentStatus(SwOrderModel $order, $statusId): SwOrderModel
    {
        $order->setPaymentStatus($this->dataProvider->getStatusReference($statusId));

        return $order;
    }

    protected function automaticTriggersAreDisabled(): bool
    {
        $automaticTriggersEnabled = (int) $this->configReader->get('automatic_triggers_enabled', 0);

        return $automaticTriggersEnabled === 0;
    }

    protected function isNotAKlarnaOrder(SwOrderModel $swOrder): bool
    {
        return !$this->paymentInsights->isKlarnaPaymentMethodId(
            $swOrder->getPayment()->getId()
        );
    }
}
