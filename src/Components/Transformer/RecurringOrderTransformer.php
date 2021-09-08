<?php

declare(strict_types=1);

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

    public function __construct(TaxHelper $taxHelper, CalculatorInterface $calculator, LineItemTransformerInterface $lineItemTransformer)
    {
        $this->taxHelper = $taxHelper;
        $this->calculator = $calculator;
        $this->lineItemTransformer = $lineItemTransformer;
    }

    public function toKlarnaOrder(array $basketData, array $userData, string $currency, $locale): RecurringOrder
    {
        $orderModel = new RecurringOrder();

        $orderModel->locale = $locale;
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

            $this->lineItemTransformer->withShippingCosts(
                $basketData['sShippingcostsWithTax'],
                $basketData['sShippingcostsNet'],
                $basketData['sShippingcostsTax'],
                $proportional
            );
        }

        $orderModel->orderLines = $this->lineItemTransformer->toKlarnaModelList($basketData['content']);

        return $orderModel;
    }
}
