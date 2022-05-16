<?php

declare(strict_types=1);

namespace BestitKlarnaOrderManagement\Components\Trigger;

use BestitKlarnaOrderManagement\Components\Api\Response;
use BestitKlarnaOrderManagement\Components\Storage\DataProvider;
use BestitKlarnaOrderManagement\Components\Trigger\Helper\OrderUpdater;

/**
 * Synchronizes the line item changes with Klarna.
 *
 * @author  Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 * @author Senan Sharhan <senan.sharhan@bestit-online.de>
 */
class LineItemDeleted
{
    /** @var OrderUpdater */
    protected $orderUpdater;
    /** @var DataProvider $dataProvider */
    protected $dataProvider;

    public function __construct(OrderUpdater $orderUpdater, DataProvider $dataProvider)
    {
        $this->orderUpdater = $orderUpdater;
        $this->dataProvider = $dataProvider;
    }

    /**
     * @param int $orderId
     */
    public function execute($orderId, array $positions): Response
    {
        $positionIds  = array_column($positions, 'id');
        $orderDetails = $this->dataProvider->getOrderDetails($orderId);

        foreach ($orderDetails as $key => $orderDetail) {
            if (!in_array($orderDetail['id'], $positionIds)) {
                continue;
            }

            unset($orderDetails[$key]);
        }

        return $this->orderUpdater->execute($orderId, $orderDetails);
    }
}
