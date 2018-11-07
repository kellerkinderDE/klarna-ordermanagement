<?php

namespace BestitKlarnaOrderManagement\Components\Transformer;

use BestitKlarnaOrderManagement\Components\Api\Model\BillingAddress;
use BestitKlarnaOrderManagement\Components\Api\Model\LineItem;
use BestitKlarnaOrderManagement\Components\Api\Model\ShippingAddress;
use BestitKlarnaOrderManagement\Components\Calculator\CalculatorInterface;
use BestitKlarnaOrderManagement\Components\Converter\ModeConverter;
use BestitKlarnaOrderManagement\Components\DataFormatter\BreadcrumbBuilderInterface;
use BestitKlarnaOrderManagement\Components\DataFormatter\ProductUrlBuilderInterface;
use Shopware\Models\Order\Billing as SwOrderBillingModel;
use Shopware\Models\Order\Shipping as SwOrderShippingModel;

/**
 * Convert Shopware Order models to klarna models
 *
 * @package BestitKlarnaOrderManagement\Components\Transformer
 *
 * @author Senan Sharhan <senan.sharhan@bestit-online.de>
 */
class OrderTransformer implements OrderTransformerInterface
{
    /** @var CalculatorInterface */
    protected $calculator;
    /** @var ModeConverter */
    protected $modeConverter;
    /** @var ProductUrlBuilderInterface */
    protected $productUrlBuilder;
    /** @var BreadcrumbBuilderInterface */
    protected $breadcrumbBuilder;
    /** @var ProductIdentifiersTransformerInterface */
    protected $productIdentifiersTransformer;

    /**
     * @param CalculatorInterface                    $calculator
     * @param ModeConverter                          $modeConverter
     * @param ProductUrlBuilderInterface             $productUrlBuilder
     * @param BreadcrumbBuilderInterface             $breadcrumbBuilder
     * @param ProductIdentifiersTransformerInterface $productIdentifiersTransformer
     */
    public function __construct(
        CalculatorInterface $calculator,
        ModeConverter $modeConverter,
        ProductUrlBuilderInterface $productUrlBuilder,
        BreadcrumbBuilderInterface $breadcrumbBuilder,
        ProductIdentifiersTransformerInterface $productIdentifiersTransformer
    ) {
        $this->calculator = $calculator;
        $this->modeConverter = $modeConverter;
        $this->productUrlBuilder = $productUrlBuilder;
        $this->breadcrumbBuilder = $breadcrumbBuilder;
        $this->productIdentifiersTransformer = $productIdentifiersTransformer;
    }

    /**
     * @param SwOrderShippingModel $shipping
     *
     * @return ShippingAddress
     */
    public function createShippingAddress(SwOrderShippingModel $shipping)
    {
        $shippingAddress = new ShippingAddress();

        $shippingAddress->givenName = $shipping->getFirstName();
        $shippingAddress->familyName = $shipping->getLastName();
        $shippingAddress->title = $shipping->getTitle() ?: null;
        $shippingAddress->streetAddress = $shipping->getStreet();
        $shippingAddress->streetAddress2 = $shipping->getCompany() ?: null;
        $shippingAddress->postalCode = $shipping->getZipCode();
        $shippingAddress->city = $shipping->getCity();
        $shippingAddress->region = $shipping->getState() ? $shipping->getState()->getName() : null;
        $shippingAddress->country = $shipping->getCountry()->getIso();
        $shippingAddress->email = $shipping->getCustomer()->getEmail();

        /**
         * The shipping address has a phone field since shopware 5.3.3.
         * Before that there was no method "getPhone", so we need to
         * check for its existence.
         */
        if (method_exists($shipping, 'getPhone')) {
            $shippingAddress->phone = $shipping->getPhone() ?: null;
        }

        return $shippingAddress;
    }

    /**
     * @param SwOrderBillingModel $billing
     *
     * @return BillingAddress
     */
    public function createBillingAddress(SwOrderBillingModel $billing)
    {
        $billingAddress = new BillingAddress();

        $billingAddress->givenName = $billing->getFirstName();
        $billingAddress->familyName = $billing->getLastName();
        $billingAddress->title = $billing->getTitle() ?: null;
        $billingAddress->streetAddress = $billing->getStreet();
        $billingAddress->streetAddress2 = $billing->getCompany() ?: null;
        $billingAddress->postalCode = $billing->getZipCode();
        $billingAddress->city = $billing->getCity();
        $billingAddress->region = $billing->getState() ? $billing->getState()->getName() : null;
        $billingAddress->country = $billing->getCountry()->getIso();
        $billingAddress->email = $billing->getCustomer()->getEmail();
        $billingAddress->phone = $billing->getPhone() ?: null;

        return $billingAddress;
    }

    /**
     * @param array $details
     *
     * @return LineItem[]
     */
    public function createLineItems(array $details)
    {
        $newLineItems = [];

        /**
         * Build the product URLs and the breadcrumbs for all items in one batch
         * to avoid many SQL queries.
         */
        $detailsWithUrl = $this->productUrlBuilder->addProductUrls($details);
        $detailsWithUrlAndBreadcrumb = $this->breadcrumbBuilder->addBreadcrumb($detailsWithUrl);

        foreach ($detailsWithUrlAndBreadcrumb as $detail) {
            $lineItem = new LineItem();
            $lineItem->type = $this->modeConverter->convert((int) $detail['modus'], (float) $detail['price']);
            $lineItem->reference = substr($detail['articleordernumber'], 0, 64);
            $lineItem->name = $detail['name'];
            $lineItem->quantity = (int) $detail['quantity'];
            $lineItem->quantityUnit = $detail['unit'] ?: null;
            $lineItem->unitPrice = $this->calculator->toCents($detail['price']);
            $lineItem->totalAmount = $this->calculator->toCents((float) $detail['price'] * (int) $detail['quantity']);
            $lineItem->totalDiscountAmount = 0;

            if (empty($detail['tax_rate'])) {
                $lineItem->taxRate = 0;
                $lineItem->totalTaxAmount = 0;
            } else {
                $lineItem->taxRate = $this->calculator->toCents($detail['tax_rate']);
                $lineItem->totalTaxAmount = $this->calculator->toCents(
                    ((float) $detail['price'] * (int) $detail['quantity']) / 100 * $detail['tax_rate']
                );
            }

            $lineItem->productIdentifiers = $this->productIdentifiersTransformer->toKlarnaModel($detail);
            $lineItem->productUrl = isset($detail['linkDetails']) ? $detail['linkDetails'] : null;
            $lineItem->imageUrl = isset($detail['image']) ? $detail['image'] : null;

            $newLineItems[] = $lineItem;
        }

        return $newLineItems;
    }

    /**
     * @param float $orderAmount
     *
     * @return int
     */
    public function createOrderAmount($orderAmount)
    {
        return $this->calculator->toCents($orderAmount);
    }
}
