<?php

declare(strict_types=1);

namespace BestitKlarnaOrderManagement\Components\SignatureGenerator;

/**
 * Generates a signature/hash for specific types of data.
 *
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
interface SignatureGeneratorInterface
{
    /**
     * Generates a signature for the given basket data using only relevant fields (i.e. something that affects pricing).
     */
    public function generateBasketSignature(array $basketData): string;

    /**
     * Generates a signature for the given billing address.
     */
    public function generateBillingAddressSignature(array $billingAddress): string;

    /**
     * Generates a signature for the given billing address.
     */
    public function generateShippingAddressSignature(array $shippingAddress): string;
}
