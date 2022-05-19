<?php

namespace BestitKlarnaOrderManagement\Components\Transformer;

use BestitKlarnaOrderManagement\Components\Api\Model\BillingAddress;

/**
 * Transforms shopware billing address to a Klarna model.
 */
interface BillingAddressTransformerInterface
{
    /**
     * @param array $userData in the same format that `sAdmin::sGetUserData` returns it
     */
    public function toKlarnaModel(array $userData): BillingAddress;
}
