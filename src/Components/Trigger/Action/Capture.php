<?php

namespace BestitKlarnaOrderManagement\Components\Trigger\Action;

use BestitKlarnaOrderManagement\Components\Api\Model\Order as KlarnaOrder;
use BestitKlarnaOrderManagement\Components\Facade\Capture as CaptureFacade;
use Shopware\Models\Order\Detail as SwOrderDetail;
use Shopware\Models\Order\Order as SwOrder;
use Shopware\Models\Order\Status;

/**
 * The capture action will capture the complete remaining authorized amount.
 *
 * @package BestitKlarnaOrderManagement\Components\Trigger\Action
 *
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
class Capture implements ActionInterface
{
    /** @var CaptureFacade */
    protected $captureFacade;

    /**
     * @param CaptureFacade $captureFacade
     */
    public function __construct(CaptureFacade $captureFacade)
    {
        $this->captureFacade = $captureFacade;
    }

    /**
     * @param SwOrder $swOrder
     * @param KlarnaOrder $klarnaOrder
     * @param SwOrderDetail|null $swOrderDetail
     *
     * @return int|null The payment status that should be set or null.
     */
    public function trigger(SwOrder $swOrder, KlarnaOrder $klarnaOrder, SwOrderDetail $swOrderDetail = null)
    {
        if ($klarnaOrder->remainingAuthorizedAmount <= 0) {
            return null;
        }

        $response = $this->captureFacade->create($swOrder->getTransactionId(), $klarnaOrder->remainingAuthorizedAmount);

        if ($response->isError()) {
            return Status::PAYMENT_STATE_REVIEW_NECESSARY;
        }

        return Status::PAYMENT_STATE_COMPLETELY_PAID;
    }
}
