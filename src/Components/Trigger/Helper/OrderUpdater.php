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
    private const CUSTOM_PRODUCT_MODE_OPTION = 2;
    private const CUSTOM_PRODUCT_MODE_VALUE = 3;

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

            $shippingLineItems = $this->getShippingCostLineItems($response->getResponseObject());

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
    protected function getShippingCostLineItems(KlarnaOrder $klarnaOrder)
    {
        $shippingLines = null;

        /*
         * With proportional taxes we can have more than 1 Shipping Line Item
         */
        foreach ($klarnaOrder->orderLines as $lineItem) {
            if ($lineItem['reference'] === Constants::SHIPPING_COSTS_REFERENCE) {
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
            if ($lineItem instanceof LineItem) {
                // LineItem is newly created by $this->orderTransformer->createLineItems()
                $totalAmount += $lineItem->totalAmount;
            } else {
                // LineItem is fetched from API response (= shipping costs)
                $totalAmount += $lineItem['total_amount'];
            }

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
            if ((int) $orderDetail['articleID'] === 0) {
                continue;
            }

            $orderDetails[$key]['linkDetails'] = "{$baseFile}?sViewport=detail&module=frontend&sArticle={$orderDetail['articleID']}";
            $orderDetails[$key]['image']       = $this->getMediaFile($orderDetail);
        }

        return $orderDetails;
    }

    private function isProduct(array $detail): bool
    {
        return !array_key_exists('swag_custom_products_mode', $detail)
            || (
                array_key_exists('swag_custom_products_mode', $detail)
                && !in_array((int) $detail['swag_custom_products_mode'], [self::CUSTOM_PRODUCT_MODE_OPTION, self::CUSTOM_PRODUCT_MODE_VALUE], true)
            );
    }

    private function getMediaFile(array $detail): ?string
    {
        if (!$this->isProduct($detail)) {
            return null;
        }

        $articleId   = (int)$detail['articleID'];
        $variantId   = (int)$detail['variantId'];
        $orderNumber = $detail['articleordernumber'];

        $media = $this->mediaService->getCover(
            new BaseProduct($articleId, $variantId, $orderNumber),
            $this->contextService->getShopContext()
        );

        if ($media === null) {
            return null;
        }

        return $media->getFile();
    }
}
