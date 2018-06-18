<?php

namespace BestitKlarnaOrderManagement\Components\SignatureGenerator;

/**
 * Generates a signature/hash for specific types of data.
 *
 * @package BestitKlarnaOrderManagement\Components\SignatureGenerator
 *
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
interface SignatureGeneratorInterface
{
    /**
     * Generates a signature for the given basket data using only relevant fields (i.e. something that affects pricing).
     *
     * @param array $basketData
     *
     * @return string
     */
    public function generateBasketSignature(array $basketData);

    /**
     * Generates a signature for the given billing address.
     *
     * @param array $billingAddress
     *
     * @return string
     */
    public function generateBillingAddressSignature(array $billingAddress);

    /**
     * Generates a signature for the given billing address.
     *
     * @param array $shippingAddress
     *
     * @return string
     */
    public function generateShippingAddressSignature(array $shippingAddress);
}
