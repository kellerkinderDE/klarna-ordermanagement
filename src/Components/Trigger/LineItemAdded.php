<?php

namespace BestitKlarnaOrderManagement\Components\Trigger;

use BestitKlarnaOrderManagement\Components\Api\Model\Error;
use BestitKlarnaOrderManagement\Components\Api\Response;
use BestitKlarnaOrderManagement\Components\DataProvider\DataProvider;
use BestitKlarnaOrderManagement\Components\Trigger\Helper\OrderUpdater;

/**
 * Synchronizes the line item changes with Klarna.
 *
 * @package BestitKlarnaOrderManagement\Components\Trigger
 *
 * @author  Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 * @author Senan Sharhan <senan.sharhan@bestit-online.de>
 */
class LineItemAdded
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
     * @param int    $orderId
     * @param string $articleNumber
     * @param int    $quantity
     * @param float  $price
     * @param int    $taxId
     * @param int    $mode
     *
     * @return Response
     */
    public function execute($orderId, $articleNumber, $quantity, $price, $taxId, $mode)
    {
        $orderDetails = $this->dataProvider->getOrderDetails($orderId);
        $articleDetail = $this->dataProvider->getArticleDetail($articleNumber);

        if ($articleDetail === null) {
            $error = new Error();
            $error->errorMessages = ["The article {$articleNumber} could not be found"];
            return Response::wrapError($error);
        }

        $unit = $articleDetail->getUnit() ? $articleDetail->getUnit()->getUnit() : null;
        $tax = $this->dataProvider->getTax($taxId);

        $newDetail = [
            'modus' => $mode,
            'price' => $price,
            'articleordernumber' => $articleNumber,
            'name' => $articleDetail->getArticle()->getName(),
            'quantity' => $quantity,
            'unit' => $unit,
            'tax_rate' => $tax->getTax()
        ];

        $orderDetails[] = $newDetail;

        return $this->orderUpdater->execute($orderId, $orderDetails);
    }
}
