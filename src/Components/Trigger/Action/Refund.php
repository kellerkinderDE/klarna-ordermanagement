<?php

namespace BestitKlarnaOrderManagement\Components\Trigger\Action;

use BestitKlarnaOrderManagement\Components\Api\Model\Order as KlarnaOrder;
use BestitKlarnaOrderManagement\Components\Facade\Order as OrderFacade;
use BestitKlarnaOrderManagement\Components\Facade\Refund as RefundFacade;
use Shopware\Models\Order\Detail as SwOrderDetail;
use Shopware\Models\Order\Order as SwOrder;
use Shopware\Models\Order\Status;

/**
 * The refund action will refund the complete captured amount and release any remaining authorized amount.
 * If there is no capture for the order yet, it will be cancelled.
 *
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
class Refund implements ActionInterface
{
    /** @var OrderFacade */
    protected $orderFacade;
    /** @var RefundFacade */
    protected $refundFacade;

    public function __construct(OrderFacade $orderFacade, RefundFacade $refundFacade)
    {
        $this->orderFacade  = $orderFacade;
        $this->refundFacade = $refundFacade;
    }

    /**
     * @return null|int the payment status that should be set or null
     */
    public function trigger(SwOrder $swOrder, KlarnaOrder $klarnaOrder, SwOrderDetail $swOrderDetail = null): ?int
    {
        $klarnaOrderId = $swOrder->getTransactionId();

        if (empty($klarnaOrderId)) {
            return null;
        }

        // No capture yet => just cancel the order
        if ($klarnaOrder->capturedAmount <= 0) {
            $response = $this->orderFacade->cancel($klarnaOrderId);

            if ($response->isError()) {
                return Status::PAYMENT_STATE_REVIEW_NECESSARY;
            }

            return Status::PAYMENT_STATE_THE_PROCESS_HAS_BEEN_CANCELLED;
        }

        $refundableAmount = $klarnaOrder->capturedAmount - $klarnaOrder->refundedAmount;

        if ($refundableAmount <= 0) {
            return null;
        }

        $response = $this->refundFacade->create($klarnaOrderId, $refundableAmount);

        if ($response->isError()) {
            return Status::PAYMENT_STATE_REVIEW_NECESSARY;
        }

        if ($klarnaOrder->remainingAuthorizedAmount > 0) {
            $this->orderFacade->releaseRemainingAmount($klarnaOrderId);
        }

        return Status::PAYMENT_STATE_RE_CREDITING;
    }
}
