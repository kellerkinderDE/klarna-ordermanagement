<?php

declare(strict_types=1);

namespace BestitKlarnaOrderManagement\Components\Shared;

class TaxHelper
{
    /** @var array */
    protected $userData = [];

    public function setUserdata(array $userData): void
    {
        $this->userData = $userData;
    }

    /**
     * Validates if the provided customer should get a tax free delivery
     */
    public function isTaxFreeDelivery(): bool
    {
        if (empty($this->userData)) {
            return false;
        }

        if (!empty($this->userData['additional']['countryShipping']['taxfree'])) {
            return true;
        }

        if (empty($this->userData['additional']['countryShipping']['taxfree_ustid'])) {
            return false;
        }

        if (empty($this->userData['shippingaddress']['ustid']) &&
            !empty($this->userData['billingaddress']['ustid']) &&
            !empty($this->userData['additional']['country']['taxfree_ustid'])) {
            return true;
        }

        return !empty($this->userData['shippingaddress']['ustid']);
    }
}
