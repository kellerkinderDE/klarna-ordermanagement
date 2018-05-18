<?php

namespace BestitKlarnaOrderManagement\Components\Trigger;

use BestitKlarnaOrderManagement\Components\Api\Model\Order as KlarnaOrderModel;
use BestitKlarnaOrderManagement\Components\ConfigReader;
use BestitKlarnaOrderManagement\Components\Storage\DataProvider;
use BestitKlarnaOrderManagement\Components\Facade\Order as OrderFacade;
use BestitKlarnaOrderManagement\Components\PaymentInsights;
use BestitKlarnaOrderManagement\Components\Trigger\Action\ActionFactoryInterface;
use Shopware\Models\Order\Order as SwOrderModel;
use Shopware\Models\Order\Status;

/**
 * Executes any defined triggers when the status of an order is changed.
 *
 * @package BestitKlarnaOrderManagement\Components\Trigger
 *
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 * @author Senan Sharhan <senan.sharhan@bestit-online.de>
 */
class OrderStatusChanged
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

    /**
     * @param OrderFacade            $orderFacade
     * @param ConfigReader           $configReader
     * @param PaymentInsights        $paymentInsights
     * @param DataProvider           $dataProvider
     * @param ActionFactoryInterface $actionFactory
     */
    public function __construct(
        OrderFacade $orderFacade,
        ConfigReader $configReader,
        PaymentInsights $paymentInsights,
        DataProvider $dataProvider,
        ActionFactoryInterface $actionFactory
    ) {
        $this->orderFacade = $orderFacade;
        $this->configReader = $configReader;
        $this->paymentInsights = $paymentInsights;
        $this->dataProvider = $dataProvider;
        $this->actionFactory = $actionFactory;
    }

    /**
     * @param SwOrderModel $swOrder
     *
     * @return SwOrderModel
     */
    public function executeDefinedTriggers(SwOrderModel $swOrder)
    {
        if ($this->automaticTriggersAreDisabled() || $this->isNotAKlarnaOrder($swOrder)) {
            return $swOrder;
        }

        $klarnaOrderId = $swOrder->getTransactionId();
        $statusId = $swOrder->getOrderStatus()->getId();

        $action = $this->actionFactory->create($statusId);

        if ($action === null) {
            return $swOrder;
        }

        $klarnaOrderResponse = $this->orderFacade->get($klarnaOrderId);

        if ($klarnaOrderResponse->isError()) {
            return $this->setPaymentStatus($swOrder, Status::PAYMENT_STATE_REVIEW_NECESSARY);
        }

        /** @var KlarnaOrderModel $klarnaOrder */
        $klarnaOrder = $klarnaOrderResponse->getResponseObject();

        $paymentStatus = $action->trigger($swOrder, $klarnaOrder);

        if ($paymentStatus === null) {
            return $swOrder;
        }

        return $this->setPaymentStatus($swOrder, $paymentStatus);
    }

    /**
     * @param SwOrderModel $order
     * @param int          $statusId
     *
     * @return SwOrderModel
     */
    protected function setPaymentStatus(SwOrderModel $order, $statusId)
    {
        $order->setPaymentStatus($this->dataProvider->getStatusReference($statusId));

        return $order;
    }

    /**
     * @return bool
     */
    protected function automaticTriggersAreDisabled()
    {
        $automaticTriggersEnabled = (int) $this->configReader->get('automatic_triggers_enabled', 0);

        return $automaticTriggersEnabled === 0;
    }

    /**
     * @param SwOrderModel $swOrder
     *
     * @return bool
     */
    protected function isNotAKlarnaOrder(SwOrderModel $swOrder)
    {
        return !$this->paymentInsights->isKlarnaPaymentMethodId(
            $swOrder->getPayment()->getId()
        );
    }
}
