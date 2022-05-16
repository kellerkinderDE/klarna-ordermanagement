<?php

declare(strict_types=1);

namespace BestitKlarnaOrderManagement\Components\Trigger\Action;

use BestitKlarnaOrderManagement\Components\Api\Model\Order as KlarnaOrder;
use BestitKlarnaOrderManagement\Components\Calculator\CalculatorInterface;
use BestitKlarnaOrderManagement\Components\Facade\Refund as RefundFacade;
use BestitKlarnaOrderManagement\Components\Transformer\OrderDetailTransformerInterface;
use Shopware\Models\Order\Detail as SwOrderDetail;
use Shopware\Models\Order\Order as SwOrder;
use Shopware\Models\Order\Status;
use Symfony\Component\Serializer\Serializer;

/**
 * Triggers a refund for the given order detail item.
 *
 * @author  Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
class PartialRefund implements ActionInterface
{
    /** @var RefundFacade */
    protected $refundFacade;
    /** @var CalculatorInterface */
    protected $calculator;
    /** @var OrderDetailTransformerInterface */
    protected $orderDetailTransformer;
    /** @var Serializer */
    protected $serializer;

    public function __construct(
        RefundFacade $refundFacade,
        CalculatorInterface $calculator,
        OrderDetailTransformerInterface $orderDetailTransformer,
        Serializer $serializer
    ) {
        $this->refundFacade           = $refundFacade;
        $this->calculator             = $calculator;
        $this->orderDetailTransformer = $orderDetailTransformer;
        $this->serializer             = $serializer;
    }

    /**
     * @return null|int the payment status that should be set or null
     */
    public function trigger(SwOrder $swOrder, KlarnaOrder $klarnaOrder, SwOrderDetail $swOrderDetail = null): ?int
    {
        if ($swOrderDetail === null) {
            return null;
        }

        $refundableAmount = $klarnaOrder->capturedAmount - $klarnaOrder->refundedAmount;
        $amountToRefund   = $this->calculator->toCents($swOrderDetail->getQuantity() * $swOrderDetail->getPrice());

        // Can't refund the cancelled amount
        if ($amountToRefund > $refundableAmount) {
            return null;
        }

        $klarnaOrderId = $swOrder->getTransactionId();

        if (empty($klarnaOrderId)) {
            return null;
        }

        $response = $this->refundFacade->create(
            $klarnaOrderId,
            $amountToRefund,
            [
                $this->orderDetailTransformer->createLineItem($swOrderDetail),
            ],
            'Automatic change by @shopware'
        );

        if ($response->isError()) {
            return Status::PAYMENT_STATE_REVIEW_NECESSARY;
        }

        return Status::PAYMENT_STATE_RE_CREDITING;
    }
}
