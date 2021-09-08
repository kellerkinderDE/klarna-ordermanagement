<?php

declare(strict_types=1);

namespace BestitKlarnaOrderManagement\Components\Transformer;

use BestitKlarnaOrderManagement\Components\Api\Model\LineItem;
use BestitKlarnaOrderManagement\Components\Calculator\CalculatorInterface;
use BestitKlarnaOrderManagement\Components\Constants;
use BestitKlarnaOrderManagement\Components\Converter\ModeConverter;
use BestitKlarnaOrderManagement\Components\DataFormatter\BreadcrumbBuilderInterface;
use BestitKlarnaOrderManagement\Components\DataFormatter\ProductUrlBuilderInterface;
use BestitKlarnaOrderManagement\Components\Shared\TaxHelper;
use BestitKlarnaOrderManagement\Components\Storage\DataProvider;
use Shopware\Components\Cart\Struct\Price;

/**
 * Transforms shopware line items to Klarna models.
 *
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
class LineItemTransformer implements LineItemTransformerInterface
{
    /** @var CalculatorInterface */
    protected $calculator;
    /** @var ModeConverter */
    protected $modeConverter;
    /** @var null|LineItem[] */
    protected $shippingOrderLines;
    /** @var BreadcrumbBuilderInterface */
    protected $breadcrumbBuilder;
    /** @var ProductUrlBuilderInterface */
    protected $productUrlBuilder;
    /** @var ProductIdentifiersTransformerInterface */
    protected $productIdentifiersTransformer;
    /** @var DataProvider */
    protected $dataProvider;
    /** @var TaxHelper */
    protected $taxHelper;

    public function __construct(
        CalculatorInterface $calculator,
        ModeConverter $modeConverter,
        BreadcrumbBuilderInterface $breadcrumbBuilder,
        ProductUrlBuilderInterface $productUrlBuilder,
        ProductIdentifiersTransformerInterface $productIdentifiersTransformer,
        DataProvider $dataProvider,
        TaxHelper $taxHelper
    ) {
        $this->calculator                    = $calculator;
        $this->modeConverter                 = $modeConverter;
        $this->breadcrumbBuilder             = $breadcrumbBuilder;
        $this->productUrlBuilder             = $productUrlBuilder;
        $this->productIdentifiersTransformer = $productIdentifiersTransformer;
        $this->dataProvider                  = $dataProvider;
        $this->taxHelper                     = $taxHelper;
    }

    /**
     * Transforms a list of shopware basket items to a list of klarna line items.
     *
     * @param array $lineItems in the format that `Shopware_Controllers_Frontend_Checkout::getBasket()['content']`
     *                         returns it
     *
     * @return LineItem[]
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function toKlarnaModelList(array $lineItems): array
    {
        /**
         * Build the product URLs and the breadcrumbs for all items in one batch
         * to avoid many SQL queries.
         */
        $lineItems       = $this->productUrlBuilder->addProductUrls($lineItems);
        $lineItems       = $this->breadcrumbBuilder->addBreadcrumb($lineItems);
        $klarnaLineItems = [];

        $customProductsAlreadyConverted = false;
        /*
         * This is necessary, to check if the custom products plugin already converted the configurations on the
         * confirm step itself to order line items. So we avoid double entries.
         * customProductMode 2 Option
         * customProductMode 3 Value
         */
        foreach ($lineItems as $lineItem) {
            if (isset($lineItem['customProductMode'])
                && ($lineItem['customProductMode'] === Constants::CUSTOM_PRODUCT_OPTION
                || $lineItem['customProductMode'] === Constants::CUSTOM_PRODUCT_VALUE)) {
                $customProductsAlreadyConverted = true;

                break;
            }
        }

        foreach ($lineItems as $lineItem) {
            if (isset($lineItem['proportion'])) {
                foreach ($lineItem['proportion'] as $pItem) {
                    $klarnaLineItems[] = $this->toKlarnaModel($pItem);
                }

                continue;
            }

            $klarnaLineItems[] = $this->toKlarnaModel($lineItem);

            if (!$customProductsAlreadyConverted) {
                $klarnaLineItems = array_merge($klarnaLineItems, $this->customProductAddsToKlarnaModel($lineItem));
            }
        }

        /**
         * Because shipping costs are not a line item in shopware by default,
         * we just create a "dummy" line item containing the information
         * about the shipping costs if there are any.
         */
        if ($this->shippingOrderLines !== null) {
            $klarnaLineItems = array_merge($klarnaLineItems, $this->shippingOrderLines);
        }

        return $klarnaLineItems;
    }

    /**
     * Transforms a shopware basket item to a klarna line item.
     *
     * @param array $lineItem The array key 'linkDetails' should be prepared, so that the value is already the
     *                        full URL and not a "shopware.php" URL.
     *                        The array key 'breadcrumb' should be manually included (the value needs to be a
     *                        breadcrumb to the category of the line item).
     *                        Take a look at the default implementation for more information.
     *
     * @internal This should not be used from the outside. It's only declared here so it can be easily decorated
     *           if need be.
     */
    public function toKlarnaModel(array $lineItem): LineItem
    {
        $quantityUnit = null;

        $sUnit = $lineItem['additional_details']['sUnit'];

        if (isset($sUnit['description']) && !empty($sUnit['description'])) {
            // According to the Klarna API Description it has to be 1-8 characters.
            $quantityUnit = mb_substr($sUnit['description'], 0, 8);
        }

        $orderLine       = new LineItem();
        $orderLine->type = $this->modeConverter->convert(
            (int) $lineItem['modus'],
            (float) $lineItem['priceNumeric']
        );
        // Klarna cannot handle reference which are longer than 64 characters.
        $orderLine->reference = substr($lineItem['ordernumber'], 0, 64);
        $orderLine->name      = $lineItem['articlename'];
        $orderLine->quantity  = (int) $lineItem['quantity'];

        $orderLine->quantityUnit = $quantityUnit;

        $quantity = (int) $lineItem['quantity'];

        $price = $this->taxHelper->isTaxFreeDelivery()
            ? (float) $lineItem['netprice']
            : $this->getUnitPriceWithTax($lineItem);

        $orderLine->unitPrice   = $this->calculator->toCents($price);
        $orderLine->totalAmount = $this->calculator->toCents(
            $price * $quantity
        );
        $orderLine->totalDiscountAmount = 0;

        $taxFree = empty($lineItem['tax_rate']) || $this->taxHelper->isTaxFreeDelivery();

        $orderLine->totalTaxAmount = $taxFree
            ? 0
            : $this->calculator->toCents((float) str_replace(',', '.', $lineItem['tax']));

        $orderLine->taxRate = $taxFree
            ? 0
            : $this->calculator->toCents($lineItem['tax_rate']);

        $productIdentifiers = $this->productIdentifiersTransformer->toKlarnaModel($lineItem);

        if ($productIdentifiers->hasData()) {
            $orderLine->productIdentifiers = $productIdentifiers;
        }

        $orderLine->productUrl = $lineItem['linkDetails'] ?? null;
        $orderLine->imageUrl   = $lineItem['image']['source'] ?? null;

        return $orderLine;
    }

    /**
     * Transforms a shopware basket "custom product add" item to a klarna line item.
     * This is needed to make our plugin compatible with the "Custom Products" plugin.
     *
     * @return LineItem[]
     */
    public function customProductAddsToKlarnaModel(array $lineItem): array
    {
        $orderLines = [];

        if (!isset($lineItem['customProductMode'])
            || $lineItem['customProductMode'] !== Constants::CUSTOM_PRODUCT_PRODUCT) {
            return [];
        }

        foreach ($lineItem['custom_product_adds'] as $customAdd) {
            $orderLines[] = $this->createCustomProductOrderLine($customAdd, $lineItem);

            foreach ($customAdd['values'] as $value) {
                $orderLines[] = $this->createCustomProductOrderLine($value, $lineItem);
            }
        }

        return $orderLines;
    }

    /**
     * Specifies that the list should include the given shipping costs as a line item.
     *
     * @param float   $shippingCostsWithTax
     * @param float   $shippingCostsNet
     * @param float   $shippingCostsTaxRate
     * @param Price[] $shippingcostsTaxProportional
     */
    public function withShippingCosts($shippingCostsWithTax, $shippingCostsNet, $shippingCostsTaxRate, $shippingcostsTaxProportional = null): LineItemTransformerInterface
    {
        /*
         * Make sure we start with no shipping costs, so calling this function multiple times
         * won't generate more shipping costs order lines then there really are
         */
        $this->shippingOrderLines = null;

        if ($shippingcostsTaxProportional === null) {
            $this->shippingOrderLines[] = $this->createShippingLineItem($shippingCostsWithTax, $shippingCostsNet, $shippingCostsTaxRate);

            return $this;
        }

        foreach ($shippingcostsTaxProportional as $item) {
            $this->shippingOrderLines[] = $this->createShippingLineItem($item->getPrice(), $item->getNetPrice(), $item->getTaxRate());
        }

        return $this;
    }

    /**
     * Create the given shipping costs as a line item.
     *
     * @param float $shippingCostsWithTax
     * @param float $shippingCostsNet
     * @param float $shippingCostsTaxRate
     */
    public function createShippingLineItem($shippingCostsWithTax, $shippingCostsNet, $shippingCostsTaxRate): LineItem
    {
        $taxFree = $this->taxHelper->isTaxFreeDelivery();

        $shippingOrderLine = new LineItem();

        $shippingOrderLine->type         = Constants::KLARNA_LINE_ITEM_TYPE_SHIPPING_FEE;
        $shippingOrderLine->reference    = Constants::SHIPPING_COSTS_REFERENCE;
        $shippingOrderLine->name         = Constants::SHIPPING_COSTS_REFERENCE;
        $shippingOrderLine->quantity     = 1;
        $shippingOrderLine->quantityUnit = null;

        $shippingOrderLine->unitPrice = $taxFree
            ? $this->calculator->toCents($shippingCostsNet)
            : $this->calculator->toCents($shippingCostsWithTax);

        $shippingOrderLine->taxRate = $taxFree
            ? 0
            : $this->calculator->toCents($shippingCostsTaxRate);

        $shippingOrderLine->totalAmount = $taxFree
            ? $this->calculator->toCents($shippingCostsNet)
            : $this->calculator->toCents($shippingCostsWithTax);

        $shippingOrderLine->totalTaxAmount = $taxFree
            ? 0
            : $this->calculator->toCents(
                $shippingCostsWithTax - $shippingCostsNet
            );

        $shippingOrderLine->totalDiscountAmount = 0;

        return $shippingOrderLine;
    }

    /**
     * Gets the orderamount with tax
     */
    public function getUnitPriceWithTax(array $lineItem): float
    {
        $netPriceIsShown = isset($lineItem['amountWithTax']) ? true : false;

        if ($netPriceIsShown) {
            return round(str_replace(',', '.', $lineItem['amountWithTax']) / $lineItem['quantity'], 2);
        }

        return (float) $lineItem['priceNumeric'];
    }

    /**
     * Creates an OrderLine item for the Custom Product options/values
     * This is needed to make our plugin compatible with the "Custom Products" plugin.
     * $item and $lineItem are a single item with key value pairs
     *
     * @param array $item     custom product value/option
     * @param array $lineItem lineItem
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    private function createCustomProductOrderLine(array $item, array $lineItem): LineItem
    {
        $taxFree         = $this->taxHelper->isTaxFreeDelivery();
        $orderLine       = new LineItem();
        $orderLine->type = Constants::KLARNA_LINE_ITEM_TYPE_SURCHARGE;

        // Klarna cannot handle reference which are longer than 64 characters.
        $orderLine->reference    = empty($item['ordernumber']) ? '' : substr($item['ordernumber'], 0, 64);
        $orderLine->name         = empty($item['name']) ? '' : $item['name'];
        $orderLine->quantity     = $item['is_once_surcharge'] ? 1 : (int) $lineItem['quantity'];
        $orderLine->quantityUnit = null;

        $orderLine->unitPrice = $this->calculator->toCents($item['surcharge']);

        $orderLine->totalAmount = $taxFree
            ? $this->calculator->toCents((float) $item['netPrice'] * (int) $orderLine->quantity)
            : $this->calculator->toCents((float) $item['surcharge'] * (int) $orderLine->quantity);

        $orderLine->totalDiscountAmount = 0;

        $totalTaxAmount = 0;

        if (!$taxFree && !empty($item['tax'])) {
            $taxSum         = $item['tax'] * $orderLine->quantity;
            $taxSumString   = str_replace(',', '.', (string) $taxSum);
            $totalTaxAmount = $this->calculator->toCents((float) $taxSumString);
        }

        $orderLine->totalTaxAmount = $totalTaxAmount;

        $taxRate = 0;

        if (!$taxFree && !empty($item['tax']) && $this->dataProvider->getTax($item['tax_id'])->getTax() !== null) {
            $taxRate = $this->calculator->toCents($this->dataProvider->getTax($item['tax_id'])->getTax());
        }

        $orderLine->taxRate = $taxRate;

        $productIdentifiers = $this->productIdentifiersTransformer->toKlarnaModel($lineItem);

        if ($productIdentifiers->hasData()) {
            $orderLine->productIdentifiers = $productIdentifiers;
        }

        $orderLine->productUrl = $lineItem['linkDetails'] ?? null;
        $orderLine->imageUrl   = $lineItem['image']['source'] ?? null;

        return $orderLine;
    }
}
