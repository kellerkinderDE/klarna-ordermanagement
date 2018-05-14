<?php

namespace BestitKlarnaOrderManagement\Components\Transformer;

use BestitKlarnaOrderManagement\Components\Api\Model\BillingAddress;
use BestitKlarnaOrderManagement\Components\Api\Model\LineItem;
use BestitKlarnaOrderManagement\Components\Api\Model\ShippingAddress;
use BestitKlarnaOrderManagement\Components\Calculator\CalculatorInterface;
use BestitKlarnaOrderManagement\Components\Converter\ModeConverter;
use Shopware\Models\Order\Billing as SwOrderBillingModel;
use Shopware\Models\Order\Detail;
use Shopware\Models\Order\Shipping as SwOrderShippingModel;

/**
 * Convert Shopware Order models to klarna models
 *
 * @package BestitKlarnaOrderManagement\Components\Transformer
 *
 * @author Senan Sharhan <senan.sharhan@bestit-online.de>
 */
class OrderTransformer
{
    /** @var CalculatorInterface */
    protected $calculator;
    /** @var ModeConverter */
    protected $modeConverter;

    /**
     * @param CalculatorInterface $calculator
     * @param ModeConverter       $modeConverter
     */
    public function __construct(CalculatorInterface $calculator, ModeConverter $modeConverter)
    {
        $this->calculator = $calculator;
        $this->modeConverter = $modeConverter;
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

        foreach ($details as $detail) {
            $lineItem = new LineItem();
            $lineItem->type = $this->modeConverter->convert((int) $detail['modus'], (float) $detail['price']);
            $lineItem->reference = substr($detail['articleordernumber'], 0, 64);
            $lineItem->name = $detail['name'];
            $lineItem->quantity = (int) $detail['quantity'];
            $lineItem->quantityUnit = $detail['unit'] ?: null;
            $lineItem->unitPrice = $this->calculator->toCents($detail['price']);
            $lineItem->taxRate = $this->calculator->toCents($detail['tax_rate']);
            $lineItem->totalAmount = $this->calculator->toCents((float) $detail['price'] * (int) $detail['quantity']);
            $lineItem->totalDiscountAmount = 0;

            if ($detail['tax_rate'] === 0) {
                $totalTaxAmount = 0;
            } else {
                $totalTaxAmount = ((float) $detail['price'] * (int) $detail['quantity']) / 100 * $detail['tax_rate'];
            }

            $lineItem->totalTaxAmount = $this->calculator->toCents($totalTaxAmount);
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
