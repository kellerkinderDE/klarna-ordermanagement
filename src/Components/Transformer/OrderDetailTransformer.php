<?php

declare(strict_types=1);

namespace BestitKlarnaOrderManagement\Components\Transformer;

use BestitKlarnaOrderManagement\Components\Api\Model\LineItem;
use BestitKlarnaOrderManagement\Components\Calculator\CalculatorInterface;
use BestitKlarnaOrderManagement\Components\Converter\ModeConverter;
use Shopware\Models\Order\Detail;

/**
 * Convert Shopware order detail item to Klarna line item.
 *
 * @author  Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
class OrderDetailTransformer implements OrderDetailTransformerInterface
{
    /** @var CalculatorInterface */
    protected $calculator;
    /** @var ModeConverter */
    protected $modeConverter;

    public function __construct(CalculatorInterface $calculator, ModeConverter $modeConverter)
    {
        $this->calculator    = $calculator;
        $this->modeConverter = $modeConverter;
    }

    public function createLineItem(Detail $detail): LineItem
    {
        $lineItem = new LineItem();

        $lineItem->type = $this->modeConverter->convert(
            (int) $detail->getMode(),
            (float) $detail->getPrice()
        );
        $lineItem->reference    = substr($detail->getArticleNumber(), 0, 64);
        $lineItem->name         = $detail->getArticleName();
        $lineItem->quantity     = (int) $detail->getQuantity();
        $lineItem->quantityUnit = $detail->getUnit();
        $lineItem->unitPrice    = $this->calculator->toCents($detail->getPrice());
        $lineItem->totalAmount  = $this->calculator->toCents(
            (float) $detail->getPrice() * (int) $detail->getQuantity()
        );
        $lineItem->totalDiscountAmount = 0;

        if ($detail->getTaxRate() <= 0) {
            $lineItem->totalTaxAmount = 0;
            $lineItem->taxRate        = 0;
        } else {
            $taxRate     = $detail->getTaxRate();
            $totalAmount = $detail->getPrice() * $detail->getQuantity();

            $lineItem->totalTaxAmount = $this->calculator->toCents(
                $totalAmount / (100 + $taxRate) * $taxRate
            );
            $lineItem->taxRate = $this->calculator->toCents($detail->getTaxRate());
        }

        return $lineItem;
    }

    /**
     * @param Detail[] $details
     *
     * @return LineItem[]
     */
    public function createLineItems(array $details): array
    {
        return array_map(function (Detail $detail) {
            return $this->createLineItem($detail);
        }, $details);
    }
}
