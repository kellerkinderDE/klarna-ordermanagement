<?php

namespace BestitKlarnaOrderManagement\Components\Transformer;

use BestitKlarnaOrderManagement\Components\Api\Model\ShippingAddress;

/**
 * Transforms shopware shipping address to a Klarna model.
 *
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
interface ShippingAddressTransformerInterface
{
    /**
     * @param array $userData in the same format that `sAdmin::sGetUserData` returns it
     */
    public function toKlarnaModel(array $userData): ShippingAddress;
}
