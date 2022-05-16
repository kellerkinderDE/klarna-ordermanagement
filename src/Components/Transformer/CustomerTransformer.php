<?php

namespace BestitKlarnaOrderManagement\Components\Transformer;

use BestitKlarnaOrderManagement\Components\Api\Model\Customer;
use BestitKlarnaOrderManagement\Components\Constants;

/**
 * Transforms shopware customer to a Klarna model.
 *
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
class CustomerTransformer implements CustomerTransformerInterface
{
    /**
     * @param array $userData in the same format that `sAdmin::sGetUserData` returns it
     */
    public function toKlarnaModel(array $userData): Customer
    {
        $customer = new Customer();

        $additional            = $userData['additional'];
        $customer->dateOfBirth = $additional['user']['birthday'] ?: null;

        if (isset($userData['billingaddress']['company']) && !empty($userData['billingaddress']['company'])) {
            $customer->type  = Constants::CUSTOMER_ORGANIZATION_TYPE;
            $customer->vatId = $userData['billingaddress']['vatId']
                ?? null;
        }

        return $customer;
    }
}
