<?php

namespace BestitKlarnaOrderManagement\Components\Trigger\Action;

/**
 * Factory to create an action object from an order status id.
 *
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
interface ActionFactoryInterface
{
    /**
     * @param int $orderStatusId
     */
    public function create($orderStatusId): ?ActionInterface;

    /**
     * @param int $orderDetailStatusId
     */
    public function createForDetailStatus($orderDetailStatusId): ?ActionInterface;
}
