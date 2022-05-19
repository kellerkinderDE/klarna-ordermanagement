<?php

namespace BestitKlarnaOrderManagement\Components\Transformer;

use BestitKlarnaOrderManagement\Components\Api\Model\RecurringOrder;
use BestitKlarnaOrderManagement\Components\Calculator\CalculatorInterface;
use BestitKlarnaOrderManagement\Components\Shared\TaxHelper;

class RecurringOrderTransformer implements RecurringOrderTransformerInterface
{
    /** @var TaxHelper */
    private $taxHelper;

    /** @var CalculatorInterface */
    private $calculator;

    /** @var LineItemTransformerInterface */
    private $lineItemTransformer;

    /** @var ShippingAddressTransformerInterface */
    private $shippingAddressTransformer;

    public function __construct(TaxHelper $taxHelper, CalculatorInterface $calculator, LineItemTransformerInterface $lineItemTransformer, ShippingAddressTransformerInterface $shippingAddressTransformer)
    {
        $this->taxHelper                  = $taxHelper;
        $this->calculator                 = $calculator;
        $this->lineItemTransformer        = $lineItemTransformer;
        $this->shippingAddressTransformer = $shippingAddressTransformer;
    }

    public function toKlarnaOrder(array $basketData, array $userData, string $currency, $locale): RecurringOrder
    {
        $orderModel = new RecurringOrder();

        $orderModel->locale           = $locale;
        $orderModel->purchaseCurrency = $currency;

        $this->taxHelper->setUserdata($userData);
        $taxFree = $this->taxHelper->isTaxFreeDelivery();

        $orderModel->orderAmount = $taxFree
            ? $this->calculator->toCents($basketData['AmountNetNumeric'])
            : $this->calculator->toCents($basketData['AmountNumeric']);

        $orderModel->orderTaxAmount = $taxFree
            ? 0
            : $this->calculator->toCents($basketData['AmountNumeric']) - $this->calculator->toCents($basketData['AmountNetNumeric']);

        if (isset($basketData['sShippingcostsWithTax']) && $basketData['sShippingcostsWithTax'] > 0) {
            $proportional = $basketData['sShippingcostsTaxProportional'] ?? null;

            // AboCommerce plugin version lower 7.1.2 doesn't provide the tax rate
            $shippingTaxRate = $basketData['sShippingcostsTax'] ?? $this->calculateApproximateShippingCostTaxRate($basketData['sShippingcostsWithTax'], $basketData['sShippingcostsNet']);

            $this->lineItemTransformer->withShippingCosts(
                $basketData['sShippingcostsWithTax'],
                $basketData['sShippingcostsNet'],
                $shippingTaxRate,
                $proportional
            );
        }

        $orderModel->orderLines = $this->lineItemTransformer->toKlarnaModelList($basketData['content']);

        $orderModel->shippingAddress = $this->shippingAddressTransformer->toKlarnaModel($userData);

        return $orderModel;
    }

    private function calculateApproximateShippingCostTaxRate(float $shippingCostBrut, float $shippingcostNet): float
    {
        return $shippingCostBrut / $shippingcostNet * 100 - 100;
    }
}
