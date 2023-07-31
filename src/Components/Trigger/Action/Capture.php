<?php

namespace BestitKlarnaOrderManagement\Components\Trigger\Action;

use BestitKlarnaOrderManagement\Components\Api\Model\Order as KlarnaOrder;
use BestitKlarnaOrderManagement\Components\Facade\Capture as CaptureFacade;
use BestitKlarnaOrderManagement\Components\Transformer\OrderDetailTransformerInterface;
use Shopware\Models\Order\Detail as SwOrderDetail;
use Shopware\Models\Order\Order as SwOrder;
use Shopware\Models\Order\Status;

/**
 * The capture action will capture the complete remaining authorized amount.
 *
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
class Capture implements ActionInterface
{
    /** @var CaptureFacade */
    protected $captureFacade;

    /** @var OrderDetailTransformerInterface */
    protected $orderDetailTransformer;

    public function __construct(CaptureFacade $captureFacade, OrderDetailTransformerInterface $orderDetailTransformer)
    {
        $this->captureFacade          = $captureFacade;
        $this->orderDetailTransformer = $orderDetailTransformer;
    }

    /**
     * @return null|int the payment status that should be set or null
     */
    public function trigger(SwOrder $swOrder, KlarnaOrder $klarnaOrder, SwOrderDetail $swOrderDetail = null): ?int
    {
        if ($klarnaOrder->remainingAuthorizedAmount <= 0) {
            return null;
        }

        $klarnaOrderId = $swOrder->getTransactionId();

        if (empty($klarnaOrderId)) {
            return null;
        }

        $lineItems = null;

        if ($klarnaOrder->remainingAuthorizedAmount === $klarnaOrder->orderAmount) {
            $lineItems = $this->orderDetailTransformer->createLineItems($swOrder->getDetails()->toArray());
        }

        $response = $this->captureFacade->create(
            $swOrder->getTransactionId(),
            $klarnaOrder->remainingAuthorizedAmount,
            $lineItems
        );

        if ($response->isError()) {
            return Status::PAYMENT_STATE_REVIEW_NECESSARY;
        }

        return Status::PAYMENT_STATE_COMPLETELY_PAID;
    }
}
