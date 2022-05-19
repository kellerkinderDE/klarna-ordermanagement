<?php

namespace BestitKlarnaOrderManagement\Components\Trigger\Action;

use BestitKlarnaOrderManagement\Components\ConfigReader;

/**
 * Factory to create an action object from an order status id.
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

    public function __construct(
        ConfigReader $configReader,
        Capture $captureAction,
        Refund $refundAction,
        PartialCapture $partialCaptureAction,
        PartialRefund $partialRefundAction
    ) {
        $this->configReader         = $configReader;
        $this->captureAction        = $captureAction;
        $this->refundAction         = $refundAction;
        $this->partialCaptureAction = $partialCaptureAction;
        $this->partialRefundAction  = $partialRefundAction;
    }

    /**
     * @param int $orderStatusId
     */
    public function create($orderStatusId): ?ActionInterface
    {
        /*
         * Since int casting of null,  " " or "" is 0 and the id 0 is the order status open we do a trim before
         * and check it. Otherwise, we might end up with captures/refunds when the order status open is set
         */
        $captureOn = trim($this->configReader->get('capture_on')) === '' ? '' : (int) $this->configReader->get('capture_on');
        $refundOn  = trim($this->configReader->get('refund_on')) === '' ? '' : (int) $this->configReader->get('refund_on');

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
     */
    public function createForDetailStatus($orderDetailStatusId): ?ActionInterface
    {
        /*
         * Since int casting of null,  " " or "" is 0 and the id 0 is the order status open we do a trim before
         * and check it. Otherwise, we might end up with captures/refunds when the order status open is set
         */
        $partialCaptureOn = trim($this->configReader->get('partial_capture_on_position_status')) === '' ? '' : (int) $this->configReader->get('partial_capture_on_position_status');
        $partialRefundOn  = trim($this->configReader->get('partial_refund_on_position_status')) === '' ? '' : (int) $this->configReader->get('partial_refund_on_position_status');

        if ($orderDetailStatusId === $partialCaptureOn) {
            return $this->partialCaptureAction;
        }

        if ($orderDetailStatusId === $partialRefundOn) {
            return $this->partialRefundAction;
        }

        return null;
    }
}
