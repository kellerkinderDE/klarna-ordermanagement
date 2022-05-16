<?php

namespace BestitKlarnaOrderManagement\Components\Transformer;

use BestitKlarnaOrderManagement\Components\Api\Model\RecurringOrder;

interface RecurringOrderTransformerInterface
{
    public function toKlarnaOrder(array $basketData, array $userData, string $currency, $locale): RecurringOrder;
}
