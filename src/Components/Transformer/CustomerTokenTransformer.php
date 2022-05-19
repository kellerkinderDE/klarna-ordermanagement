<?php

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
        $this->customerTransformer       = $customerTransformer;
        $this->billingAddressTransformer = $billingAddressTransformer;
    }

    public function toKlarnaModel(string $purchaseCountry, string $currency, string $locale): CustomerTokenModel
    {
        $customerTokenModel = new CustomerTokenModel();

        $customerTokenModel->purchaseCountry  = $purchaseCountry;
        $customerTokenModel->purchaseCurrency = $currency;
        $customerTokenModel->locale           = $locale;

        $customerTokenModel->merchantUrls = $this->merchantUrls;

        if (!empty($this->userData)) {
            $customerTokenModel->customer       = $this->customerTransformer->toKlarnaModel($this->userData);
            $customerTokenModel->billingAddress = $this->billingAddressTransformer->toKlarnaModel($this->userData);
        }

        return $customerTokenModel;
    }

    public function withUserData(array $userData): CustomerTokenTransformerInterface
    {
        $this->userData = $userData;

        return $this;
    }

    public function withMerchantUrls(string $confirmationUrl): CustomerTokenTransformerInterface
    {
        $this->merchantUrls = [
            'confirmation' => $confirmationUrl,
        ];

        return $this;
    }
}
