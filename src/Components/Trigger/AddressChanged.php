<?php

declare(strict_types=1);

namespace BestitKlarnaOrderManagement\Components\Trigger;

use BestitKlarnaOrderManagement\Components\Api\Response;
use BestitKlarnaOrderManagement\Components\Facade\Order as OrderFacade;
use BestitKlarnaOrderManagement\Components\Storage\DataProvider;
use BestitKlarnaOrderManagement\Components\Transformer\OrderTransformer;
use Shopware\Models\Order\Billing;
use Shopware\Models\Order\Shipping;

/**
 * Synchronizes the address changes with Klarna.
 *
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 * @author Senan Sharhan <senan.sharhan@bestit-online.de>
 */
class AddressChanged
{
    /** @var DataProvider */
    protected $dataProvider;
    /** @var OrderFacade */
    protected $orderFacade;
    /** @var OrderTransformer */
    protected $orderTransformer;

    public function __construct(
        DataProvider $dataProvider,
        OrderFacade $orderFacade,
        OrderTransformer $orderTransformer
    ) {
        $this->dataProvider     = $dataProvider;
        $this->orderFacade      = $orderFacade;
        $this->orderTransformer = $orderTransformer;
    }

    /**
     * @param int $orderId
     */
    public function execute($orderId, array $shippingData, array $billingData): Response
    {
        $shippingData = $this->loadCountryAndStateForAddress($shippingData);
        $billingData  = $this->loadCountryAndStateForAddress($billingData);

        $shopwareOrder = $this->dataProvider->getSwOrder($orderId);

        /**
         * We clone the old address and do not operate on it directly to avoid
         * issues with Doctrine - otherwise it will try to actually do updates
         * on that model which is not the wanted behaviour.
         */
        $oldShipping = $shopwareOrder->getShipping();
        $newShipping = clone $oldShipping;
        /** @var Shipping $newShipping */
        $newShipping = $newShipping->fromArray($shippingData);

        $oldBilling = $shopwareOrder->getBilling();
        $newBilling = clone $oldBilling;
        /** @var Billing $newBilling */
        $newBilling = $newBilling->fromArray($billingData);

        if (!$this->isAddressChanged($oldShipping, $newShipping) &&
            !$this->isAddressChanged($oldBilling, $newBilling)
        ) {
            return Response::wrapEmptySuccessResponse();
        }

        $billing  = $this->orderTransformer->createBillingAddress($newBilling);
        $shipping = $this->orderTransformer->createShippingAddress($newShipping);

        return $this->orderFacade->updateAddresses($shopwareOrder->getTransactionId(), $shipping, $billing);
    }

    protected function loadCountryAndStateForAddress(array $address): array
    {
        if (isset($address['countryId']) && !empty($address['countryId'])) {
            $address['country'] = $this->dataProvider->getCountry($address['countryId']);
        }

        if (isset($address['stateId']) && !empty($address['stateId'])) {
            $address['state'] = $this->dataProvider->getState($address['stateId']);
        }

        return $address;
    }

    /**
     * @param Billing|Shipping $oldAddress
     * @param Billing|Shipping $newAddress
     */
    protected function isAddressChanged($oldAddress, $newAddress): bool
    {
        /**
         * Do not use strict equality comparison - the objects don't have to be the same.
         * They just need to be equal (have the same values). They will always be different
         * objects.
         */
        return $oldAddress != $newAddress;
    }
}
