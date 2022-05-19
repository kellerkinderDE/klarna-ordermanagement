<?php

namespace BestitKlarnaOrderManagement\Components\Transformer;

use BestitKlarnaOrderManagement\Components\Api\Model\BillingAddress;

/**
 * Transforms shopware billing address to a Klarna model.
 */
class BillingAddressTransformer implements BillingAddressTransformerInterface
{
    /**
     * @param array $userData in the same format that `sAdmin::sGetUserData` returns it
     */
    public function toKlarnaModel(array $userData): BillingAddress
    {
        $billingAddress = new BillingAddress();

        $billingAddress->phone      = $userData['billingaddress']['phone'];
        $billingAddress->email      = $userData['additional']['user']['email'];
        $billingAddress->country    = $userData['additional']['country']['countryiso'];
        $billingAddress->postalCode = $userData['billingaddress']['zipcode'];

        if (isset($userData['additional']['state'])) {
            $billingAddress->region = $userData['additional']['state']['statename'];
        }

        $billingAddress->city           = $userData['billingaddress']['city'];
        $billingAddress->streetAddress  = $userData['billingaddress']['street'];
        $billingAddress->streetAddress2 = null;
        $billingAddress->title          = $userData['billingaddress']['salutation'];
        $billingAddress->givenName      = $userData['billingaddress']['firstname'];
        $billingAddress->familyName     = $userData['billingaddress']['lastname'];

        if (isset($userData['billingaddress']['company']) && !empty($userData['billingaddress']['company'])) {
            $billingAddress->organizationName = $userData['billingaddress']['company'];
        }

        return $billingAddress;
    }
}
