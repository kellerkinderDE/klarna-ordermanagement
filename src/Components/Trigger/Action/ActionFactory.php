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

    /**
     * @param ConfigReader    $configReader
     * @param ActionInterface $captureAction
     * @param ActionInterface $refundAction
     */
    public function __construct(
        ConfigReader $configReader,
        ActionInterface $captureAction,
        ActionInterface $refundAction
    ) {
        $this->configReader = $configReader;
        $this->captureAction = $captureAction;
        $this->refundAction = $refundAction;
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
}
