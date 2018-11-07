<?php

namespace BestitKlarnaOrderManagement\Components\Trigger\Action;

/**
 * Factory to create an action object from an order status id.
 *
 * @package BestitKlarnaOrderManagement\Components\Trigger\Action
 *
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
interface ActionFactoryInterface
{
    /**
     * @param int $orderStatusId
     *
     * @return ActionInterface|null
     */
    public function create($orderStatusId);

    /**
     * @param int $orderDetailStatusId
     *
     * @return ActionInterface|null
     */
    public function createForDetailStatus($orderDetailStatusId);
}
