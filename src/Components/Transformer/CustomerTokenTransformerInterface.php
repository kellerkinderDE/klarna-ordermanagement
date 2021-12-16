<?php

declare(strict_types=1);

namespace BestitKlarnaOrderManagement\Components\Transformer;

use BestitKlarnaOrderManagement\Components\Api\Model\CustomerToken as CustomerTokenModel;

interface CustomerTokenTransformerInterface
{
    public function toKlarnaModel(string $purchaseCountry, string $currency, string $locale): CustomerTokenModel;

    public function withUserData(array $userData): CustomerTokenTransformerInterface;

    /**
     * @param string $confirmationUrl
     * @return CustomerTokenTransformerInterface
     */
    public function withMerchantUrls(string $confirmationUrl): CustomerTokenTransformerInterface;
}
