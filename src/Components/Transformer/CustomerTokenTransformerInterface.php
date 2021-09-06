<?php

declare(strict_types=1);

namespace BestitKlarnaOrderManagement\Components\Transformer;

use BestitKlarnaOrderManagement\Components\Api\Model\CustomerToken as CustomerTokenModel;

interface CustomerTokenTransformerInterface
{
    /**
     * @param string $purchaseCountry
     * @param string $locale
     * @param string $description
     * @param string $intendedUse
     * @return CustomerTokenModel
     */
    public function toKlarnaModel($purchaseCountry, $currency, $locale, $description, $intendedUse): CustomerTokenModel;

    public function withUserData(array $userData): CustomerTokenTransformerInterface;

    /**
     * @param string $confirmationUrl
     * @return CustomerTokenTransformerInterface
     */
    public function withMerchantUrls(string $confirmationUrl): CustomerTokenTransformerInterface;
}
