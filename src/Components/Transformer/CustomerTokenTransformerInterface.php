<?php

namespace BestitKlarnaOrderManagement\Components\Transformer;

use BestitKlarnaOrderManagement\Components\Api\Model\CustomerToken as CustomerTokenModel;

interface CustomerTokenTransformerInterface
{
    public function toKlarnaModel(string $purchaseCountry, string $currency, string $locale): CustomerTokenModel;

    public function withUserData(array $userData): CustomerTokenTransformerInterface;

    public function withMerchantUrls(string $confirmationUrl): CustomerTokenTransformerInterface;
}
