<?php

namespace BestitKlarnaOrderManagement\Components\Trigger;

use BestitKlarnaOrderManagement\Components\Api\Response;
use BestitKlarnaOrderManagement\Components\DataProvider\DataProvider;
use BestitKlarnaOrderManagement\Components\Trigger\Helper\OrderUpdater;

/**
 * Synchronizes the line item changes with Klarna.
 *
 * @package BestitKlarnaOrderManagement\Components\Trigger
 *
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 * @author Senan Sharhan <senan.sharhan@bestit-online.de>
 */
class LineItemChanged
{
    /** @var OrderUpdater */
    protected $orderUpdater;
    /** @var DataProvider $dataProvider */
    protected $dataProvider;

    /**
     * @param OrderUpdater $orderUpdater
     * @param DataProvider $dataProvider
     */
    public function __construct(OrderUpdater $orderUpdater, DataProvider $dataProvider)
    {
        $this->orderUpdater = $orderUpdater;
        $this->dataProvider = $dataProvider;
    }

    /**
     * @param int   $orderId
     * @param array $position
     *
     * @return Response
     */
    public function execute($orderId, array $position)
    {
        $orderDetails = $this->dataProvider->getOrderDetails($orderId);

        foreach ($orderDetails as $key => $orderDetail) {
            if ($position['id'] != $orderDetail['id']) {
                continue;
            }

            $orderDetails[$key]['quantity'] = $position['quantity'];
            $orderDetails[$key]['price'] = $position['price'];
        }

        return $this->orderUpdater->execute($orderId, $orderDetails);
    }
}
