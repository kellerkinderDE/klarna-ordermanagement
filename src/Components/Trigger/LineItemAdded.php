<?php
declare(strict_types=1);

namespace BestitKlarnaOrderManagement\Components\Trigger;

use BestitKlarnaOrderManagement\Components\Api\Response;
use BestitKlarnaOrderManagement\Components\Storage\DataProvider;
use BestitKlarnaOrderManagement\Components\Trigger\Helper\OrderUpdater;
use Shopware\Models\Article\Detail;

/**
 * Synchronizes the line item changes with Klarna.
 *
 * @author  Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 * @author Senan Sharhan <senan.sharhan@bestit-online.de>
 */
class LineItemAdded
{
    /** @var OrderUpdater */
    protected $orderUpdater;

    /** @var DataProvider */
    protected $dataProvider;

    public function __construct(OrderUpdater $orderUpdater, DataProvider $dataProvider)
    {
        $this->orderUpdater = $orderUpdater;
        $this->dataProvider = $dataProvider;
    }

    public function execute(int $orderId, string $articleNumber, int $quantity, float $price, int $taxId, int $mode, string $articleName): Response
    {
        $orderDetails  = $this->dataProvider->getOrderDetails($orderId);
        $articleDetail = $this->dataProvider->getArticleDetail($articleNumber);
        $tax           = $this->dataProvider->getTax($taxId);

        $variantId = $articleId = $unit = null;

        if ($articleDetail instanceof Detail) {
            $variantId   = $articleDetail->getId();
            $articleId   = $articleDetail->getArticleId();
            $articleName = empty($articleName) ? $articleDetail->getArticle()->getName() : $articleName;
            $unit        = $articleDetail->getUnit() === null ? null : $articleDetail->getUnit()->getUnit();
        }

        $newDetail = [
            'variantId'          => $variantId,
            'articleID'          => $articleId,
            'modus'              => $mode,
            'price'              => $price,
            'articleordernumber' => $articleNumber,
            'name'               => $articleName,
            'quantity'           => $quantity,
            'unit'               => $unit,
            'tax_rate'           => $tax->getTax(),
        ];

        $orderDetails[] = $newDetail;

        return $this->orderUpdater->execute($orderId, $orderDetails);
    }
}
