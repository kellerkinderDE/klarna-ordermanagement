<?php

namespace BestitKlarnaOrderManagement\Components\Trigger\Action;

use BestitKlarnaOrderManagement\Components\ConfigReader;

/**
 * Factory to create an action object from an order status id.
 *
 * @package BestitKlarnaOrderManagement\Components\Trigger\Action
 *
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
class ActionFactory implements ActionFactoryInterface
{
    /** @var ConfigReader */
    protected $configReader;
    /** @var ActionInterface */
    protected $captureAction;
    /** @var ActionInterface */
    protected $refundAction;
    /** @var PartialCapture */
    protected $partialCaptureAction;
    /** @var PartialRefund */
    protected $partialRefundAction;

    /**
     * @param ConfigReader $configReader
     * @param Capture $captureAction
     * @param Refund $refundAction
     * @param PartialCapture $partialCaptureAction
     * @param PartialRefund $partialRefundAction
     */
    public function __construct(
        ConfigReader $configReader,
        Capture $captureAction,
        Refund $refundAction,
        PartialCapture $partialCaptureAction,
        PartialRefund $partialRefundAction
    ) {
        $this->configReader = $configReader;
        $this->captureAction = $captureAction;
        $this->refundAction = $refundAction;
        $this->partialCaptureAction = $partialCaptureAction;
        $this->partialRefundAction = $partialRefundAction;
    }

    /**
     * @param int $orderStatusId
     *
     * @return ActionInterface|null
     */
    public function create($orderStatusId)
    {
        $captureOn = (int) $this->configReader->get('capture_on');
        $refundOn = (int) $this->configReader->get('refund_on');

        if ($orderStatusId === $captureOn) {
            return $this->captureAction;
        }

        if ($orderStatusId === $refundOn) {
            return $this->refundAction;
        }

        return null;
    }

    /**
     * @param int $orderDetailStatusId
     *
     * @return ActionInterface|null
     */
    public function createForDetailStatus($orderDetailStatusId)
    {
        $partialCaptureOn = (int) $this->configReader->get('partial_capture_on_position_status');
        $partialRefundOn = (int) $this->configReader->get('partial_refund_on_position_status');

        if ($orderDetailStatusId === $partialCaptureOn) {
            return $this->partialCaptureAction;
        }

        if ($orderDetailStatusId === $partialRefundOn) {
            return $this->partialRefundAction;
        }

        return null;
    }
}
