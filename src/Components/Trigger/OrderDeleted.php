<?php

declare(strict_types=1);

namespace BestitKlarnaOrderManagement\Components\Trigger;

use BestitKlarnaOrderManagement\Components\Api\Response;
use BestitKlarnaOrderManagement\Components\Facade\Order as OrderFacade;
use BestitKlarnaOrderManagement\Components\Storage\DataProvider;

/**
 * Cancels the order in Klarna when an order is deleted in shopware.
 *
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 * @author Senan Sharhan <senan.sharhan@bestit-online.de>
 */
class OrderDeleted
{
    /** @var DataProvider */
    protected $dataProvider;
    /** @var OrderFacade */
    protected $orderFacade;

    public function __construct(DataProvider $dataProvider, OrderFacade $orderFacade)
    {
        $this->dataProvider = $dataProvider;
        $this->orderFacade  = $orderFacade;
    }

    /**
     * @param int $orderId
     */
    public function execute($orderId): Response
    {
        return $this->orderFacade->cancel(
            $this->dataProvider->getKlarnaOrderId($orderId)
        );
    }
}
