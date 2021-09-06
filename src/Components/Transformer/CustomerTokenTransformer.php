<?php

declare(strict_types=1);

namespace BestitKlarnaOrderManagement\Components\Transformer;

use BestitKlarnaOrderManagement\Components\Api\Model\CustomerToken as CustomerTokenModel;

class CustomerTokenTransformer implements CustomerTokenTransformerInterface
{
    /** @var array */
    private $userData = [];

    /** @var null|array */
    private $merchantUrls;

    /** @var CustomerTransformerInterface */
    private $customerTransformer;

    /** @var BillingAddressTransformerInterface */
    private $billingAddressTransformer;

    public function __construct(CustomerTransformerInterface $customerTransformer, BillingAddressTransformerInterface $billingAddressTransformer)
    {
        $this->customerTransformer = $customerTransformer;
        $this->billingAddressTransformer = $billingAddressTransformer;
    }

    /**
     * @param string $purchaseCountry
     * @param string $locale
     * @param string $description
     * @param string $intendedUse
     * @return CustomerTokenModel
     */
    public function toKlarnaModel($purchaseCountry, $currency, $locale, $description, $intendedUse): CustomerTokenModel
    {
        $customerTokenModel = new CustomerTokenModel();

        $customerTokenModel->purchaseCountry = $purchaseCountry;
        $customerTokenModel->purchaseCurrency = $currency;
        $customerTokenModel->locale = $locale;
        $customerTokenModel->description = $description;
        $customerTokenModel->intendedUse = $intendedUse;

        $customerTokenModel->merchantUrls = $this->merchantUrls;

        if (!empty($this->userData)) {
            $customerTokenModel->customer = $this->customerTransformer->toKlarnaModel($this->userData);
            $customerTokenModel->billingAddress  = $this->billingAddressTransformer->toKlarnaModel($this->userData);
        }

        return $customerTokenModel;
    }

    public function withUserData(array $userData): CustomerTokenTransformerInterface
    {
        $this->userData = $userData;

        return $this;
    }

    /**
     * @param string $confirmationUrl
     * @return CustomerTokenTransformerInterface
     */
    public function withMerchantUrls(string $confirmationUrl): CustomerTokenTransformerInterface
    {
        $this->merchantUrls = [
            'confirmation' => $confirmationUrl,
        ];

        return $this;
    }
}
