<?php

declare(strict_types=1);

namespace BestitKlarnaOrderManagement\Components\Transformer;

use BestitKlarnaOrderManagement\Components\Api\Model\ShippingAddress;

/**
 * Transforms shopware shipping address to a Klarna model.
 *
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
class ShippingAddressTransformer implements ShippingAddressTransformerInterface
{
    /**
     * @param array $userData in the same format that `sAdmin::sGetUserData` returns it
     */
    public function toKlarnaModel(array $userData): ShippingAddress
    {
        $shippingAddress = new ShippingAddress();

        $shippingAddress->phone      = $userData['shippingaddress']['phone'];
        $shippingAddress->email      = $userData['additional']['user']['email'];
        $shippingAddress->country    = $this->getShippingCountryCode($userData);
        $shippingAddress->postalCode = $userData['shippingaddress']['zipcode'];

        if (isset($userData['additional']['stateShipping'])) {
            $shippingAddress->region = $userData['additional']['stateShipping']['statename'];
        }

        $shippingAddress->city           = $userData['shippingaddress']['city'];
        $shippingAddress->streetAddress  = $userData['shippingaddress']['street'];
        $shippingAddress->streetAddress2 = $userData['shippingaddress']['company'];
        $shippingAddress->title          = $userData['shippingaddress']['salutation'];
        $shippingAddress->givenName      = $userData['shippingaddress']['firstname'];
        $shippingAddress->familyName     = $userData['shippingaddress']['lastname'];

        if (isset($userData['billingaddress']['company']) && !empty($userData['billingaddress']['company'])) {
            $shippingAddress->organizationName = $userData['billingaddress']['company'];
        }

        return $shippingAddress;
    }

    private function getShippingCountryCode(array $userData): string
    {
        if (isset($userData['additional']['country']['countryiso'])) {
            return $userData['additional']['country']['countryiso'];
        }

        //abocommerce fallback
        if (isset($userData['additional']['countryShipping']['countryiso'])) {
            return $userData['additional']['countryShipping']['countryiso'];
        }

        return '';
    }
}
