<?php

namespace BestitKlarnaOrderManagement\Components\Trigger\Action;

use BestitKlarnaOrderManagement\Components\Api\Model\Order as KlarnaOrder;
use Shopware\Models\Order\Order as SwOrder;

/**
 * Interface that has to be implemented by all automatic actions that can be triggered.
 *
 * @package BestitKlarnaOrderManagement\Components\Trigger\Action
 *
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
interface ActionInterface
{
    /**
     * @param SwOrder     $swOrder
     * @param KlarnaOrder $klarnaOrder
     *
     * @return int|null The payment status that should be set or null.
     */
    public function trigger(SwOrder $swOrder, KlarnaOrder $klarnaOrder);
}
