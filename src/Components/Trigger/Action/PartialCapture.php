<?php

namespace BestitKlarnaOrderManagement\Components\Trigger\Action;

use BestitKlarnaOrderManagement\Components\Api\Model\Order as KlarnaOrder;
use BestitKlarnaOrderManagement\Components\Calculator\CalculatorInterface;
use BestitKlarnaOrderManagement\Components\Facade\Capture as CaptureFacade;
use BestitKlarnaOrderManagement\Components\Transformer\OrderDetailTransformerInterface;
use Shopware\Models\Order\Detail as SwOrderDetail;
use Shopware\Models\Order\Order as SwOrder;
use Shopware\Models\Order\Status;
use Symfony\Component\Serializer\Serializer;

/**
 * Triggers a capture for the given order detail item.
 *
 * @author  Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
class PartialCapture implements ActionInterface
{
    /** @var CaptureFacade */
    protected $captureFacade;
    /** @var CalculatorInterface */
    protected $calculator;
    /** @var OrderDetailTransformerInterface */
    protected $detailTransformer;
    /** @var Serializer */
    protected $serializer;

    public function __construct(
        CaptureFacade $captureFacade,
        CalculatorInterface $calculator,
        OrderDetailTransformerInterface $detailTransformer,
        Serializer $serializer
    ) {
        $this->captureFacade     = $captureFacade;
        $this->calculator        = $calculator;
        $this->detailTransformer = $detailTransformer;
        $this->serializer        = $serializer;
    }

    /**
     * @return null|int the payment status that should be set or null
     */
    public function trigger(SwOrder $swOrder, KlarnaOrder $klarnaOrder, SwOrderDetail $swOrderDetail = null): ?int
    {
        if ($swOrderDetail === null) {
            return null;
        }

        $amountToCapture = $this->calculator->toCents($swOrderDetail->getPrice() * $swOrderDetail->getQuantity());

        if ($klarnaOrder->remainingAuthorizedAmount < $amountToCapture) {
            return null;
        }

        $klarnaOrderId = $swOrder->getTransactionId();

        if (empty($klarnaOrderId)) {
            return null;
        }

        $response = $this->captureFacade->create(
            $klarnaOrderId,
            $amountToCapture,
            $this->serializer->serialize(
                [$this->detailTransformer->createLineItem($swOrderDetail)],
                'json'
            ),
            'Automatic status change @shopware.'
        );

        if ($response->isError()) {
            return Status::PAYMENT_STATE_REVIEW_NECESSARY;
        }

        return Status::PAYMENT_STATE_PARTIALLY_PAID;
    }
}
