<?php

declare(strict_types=1);

namespace BestitKlarnaOrderManagement\Components\Transformer;

use BestitKlarnaOrderManagement\Components\Api\Model\Customer;

/**
 * Transforms shopware customer to a Klarna model.
 *
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
interface CustomerTransformerInterface
{
    /**
     * @param array $userData in the same format that `sAdmin::sGetUserData` returns it
     */
    public function toKlarnaModel(array $userData): Customer;
}
