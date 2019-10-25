<?php

namespace BestitKlarnaOrderManagement\Components\Trigger\Helper;

use BestitKlarnaOrderManagement\Components\Api\Model\LineItem;
use BestitKlarnaOrderManagement\Components\Api\Model\Order as KlarnaOrder;
use BestitKlarnaOrderManagement\Components\Api\Response;
use BestitKlarnaOrderManagement\Components\Constants;
use BestitKlarnaOrderManagement\Components\Storage\DataProvider;
use BestitKlarnaOrderManagement\Components\Facade\Order as OrderFacade;
use BestitKlarnaOrderManagement\Components\Transformer\OrderTransformer;
use Shopware\Bundle\StoreFrontBundle\Service\ContextServiceInterface;
use Shopware\Bundle\StoreFrontBundle\Service\MediaServiceInterface;
use Shopware\Bundle\StoreFrontBundle\Struct\BaseProduct;
use Shopware_Components_Config;

/**
 * Updates the order with the given line items.
 *
 * @package BestitKlarnaOrderManagement\Components\Trigger\Helper
 *
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 * @author Senan Sharhan <senan.sharhan@bestit-online.de>
 */
class OrderUpdater
{
    /** @var OrderFacade */
    protected $orderFacade;
    /** @var OrderTransformer */
    protected $orderTransformer;
    /** @var DataProvider */
    protected $dataProvider;
    /** @var Shopware_Components_Config */
    protected $config;
    /** @var MediaServiceInterface */
    protected $mediaService;
    /** @var ContextServiceInterface */
    protected $contextService;

    /**
     * @param OrderFacade                $orderFacade
     * @param OrderTransformer           $orderTransformer
     * @param DataProvider               $dataProvider
     * @param Shopware_Components_Config $config
     * @param MediaServiceInterface      $mediaService
     * @param ContextServiceInterface    $contextService
     */
    public function __construct(
        OrderFacade $orderFacade,
        OrderTransformer $orderTransformer,
        DataProvider $dataProvider,
        Shopware_Components_Config $config,
        MediaServiceInterface $mediaService,
        ContextServiceInterface $contextService
    ) {
        $this->orderFacade = $orderFacade;
        $this->orderTransformer = $orderTransformer;
        $this->dataProvider = $dataProvider;
        $this->config = $config;
        $this->mediaService = $mediaService;
        $this->contextService = $contextService;
    }

    /**
     * @param int   $orderId
     * @param array $orderDetails
     *
     * @return Response
     */
    public function execute($orderId, array $orderDetails)
    {
        $orderDetails = $this->addProductInformationToOrderDetails($orderDetails);

        $swOrder = $this->dataProvider->getSwOrder($orderId);

        $shippingCosts = $swOrder->getInvoiceShipping() ?: 0;
        $klarnaOrderId = $swOrder->getTransactionId();

        $lineItems = $this->orderTransformer->createLineItems($orderDetails);

        if ($this->shippingCostsExist($shippingCosts)) {
            $response = $this->orderFacade->get($klarnaOrderId);

            if ($response->isError()) {
                return $response;
            }

            $shippingLineItems = $this->getShippingCostLineItem($response->getResponseObject());

            if ($shippingLineItems !== null) {
                $lineItems = array_merge($lineItems, $shippingLineItems);
            }
        }

        return $this->orderFacade->updateOrder($klarnaOrderId, $this->calculateTotalAmount($lineItems), $lineItems);
    }

    /**
     * @param float $shippingCosts
     *
     * @return bool
     */
    protected function shippingCostsExist($shippingCosts)
    {
        return $shippingCosts >= 0;
    }

    /**
     * @param KlarnaOrder $klarnaOrder
     *
     * @return LineItem[]|null
     */
    protected function getShippingCostLineItem(KlarnaOrder $klarnaOrder)
    {
        $shippingLines = null;

        /*
         * With proportional taxes we can have more than 1 Shipping Line Item
         */
        foreach ($klarnaOrder->orderLines as $lineItem) {
            if ($lineItem->reference === Constants::SHIPPING_COSTS_REFERENCE) {
                $shippingLines[] = $lineItem;
            }
        }

        return $shippingLines;
    }

    /**
     * @param LineItem[] $lineItems
     *
     * @return float
     */
    protected function calculateTotalAmount(array $lineItems)
    {
        $totalAmount = 0.00;

        foreach ($lineItems as $lineItem) {
            $totalAmount += $lineItem->totalAmount;
        }

        return $totalAmount;
    }

    /**
     * @param array $orderDetails
     *
     * @return array
     */
    protected function addProductInformationToOrderDetails(array $orderDetails)
    {
        $baseFile = $this->config->get('baseFile');

        foreach ($orderDetails as $key => $orderDetail) {
            $articleId = (int) $orderDetail['articleID'];
            $variantId = (int) $orderDetail['variantId'];
            $orderNumber = $orderDetail['articleordernumber'];

            if ($articleId === 0) {
                continue;
            }

            $linkDetails = null;
            $image = null;

            if ($articleId !== 0) {
                $linkDetails = "{$baseFile}?sViewport=detail&module=frontend&sArticle={$articleId}";

                $media = $this->mediaService->getCover(
                    new BaseProduct($articleId, $variantId, $orderNumber),
                    $this->contextService->getShopContext()
                );

                $image = $media === null ? null : $media->getFile();
            }

            $orderDetails[$key]['linkDetails'] = $linkDetails;
            $orderDetails[$key]['image'] = $image;
        }

        return $orderDetails;
    }
}
