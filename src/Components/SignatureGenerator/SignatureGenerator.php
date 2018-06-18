<?php

namespace BestitKlarnaOrderManagement\Components\SignatureGenerator;

use InvalidArgumentException;
use JsonSerializable;

/**
 * Generates a signature/hash for specific types of data.
 *
 * @package BestitKlarnaOrderManagement\Components\SignatureGenerator
 *
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
class SignatureGenerator implements SignatureGeneratorInterface
{
    /**
     * Generates a signature for the given basket data using only relevant fields (i.e. something that affects pricing).
     *
     * @param array $basketData
     *
     * @return string
     */
    public function generateBasketSignature(array $basketData)
    {
        $lineItems = $basketData['content'];
        $formattedLineItems = [];

        foreach ($lineItems as $lineItem) {
            $formattedLineItems[] = [
                'ordernumber' => $lineItem['ordernumber'],
                'price' => (float) $lineItem['price'],
                'quantity' => (int) $lineItem['quantity'],
                'tax_rate' => (int) $lineItem['tax_rate'],
            ];
        }

        return $this->createHash([
            'amount' => (float) $basketData['sAmount'],
            'lineItems' => $formattedLineItems,
            'taxAmount' => (float) $basketData['sAmountTax'],
        ]);
    }

    /**
     * Generates a signature for the given billing address.
     *
     * @param array $billingAddress
     *
     * @return string
     */
    public function generateBillingAddressSignature(array $billingAddress)
    {
        return $this->createHash($billingAddress);
    }

    /**
     * Generates a signature for the given billing address.
     *
     * @param array $shippingAddress
     *
     * @return string
     */
    public function generateShippingAddressSignature(array $shippingAddress)
    {
        return $this->createHash($shippingAddress);
    }

    /**
     * @param array|JsonSerializable $data
     *
     * @return string
     *
     * @throws InvalidArgumentException
     */
    protected function createHash($data)
    {
        if (!is_array($data) && !$data instanceof JsonSerializable) {
            throw new InvalidArgumentException('The given input can not be JSON serialized.');
        }

        return hash('sha256', json_encode($data));
    }
}
