<?php

declare(strict_types=1);

namespace BestitKlarnaOrderManagement\Components\Facade;

use BestitKlarnaOrderManagement\Components\Api\Request;
use BestitKlarnaOrderManagement\Components\Api\Resource\CustomerToken as CustomerTokenResource;
use BestitKlarnaOrderManagement\Components\Shared\AuthorizationHelper;
use BestitKlarnaOrderManagement\Components\Transformer\CustomerTokenTransformerInterface;
use Symfony\Component\Serializer\Serializer;

class CustomerToken
{
    /** @var CustomerTokenResource */
    private $customerTokenResource;

    /** @var CustomerTokenTransformerInterface */
    private $customerTokenTransformer;

    /** @var Serializer */
    private $serializer;

    /** @var AuthorizationHelper */
    private $authorizationHelper;

    public function __construct(
        CustomerTokenResource $customerTokenResource,
        CustomerTokenTransformerInterface $customerTokenTransformer,
        Serializer $serializer,
        AuthorizationHelper $authorizationHelper
    )
    {
        $this->customerTokenResource = $customerTokenResource;
        $this->customerTokenTransformer = $customerTokenTransformer;
        $this->serializer = $serializer;
        $this->authorizationHelper = $authorizationHelper;
    }

    public function create($klarnaAuthToken, array $userData, $currency, $confirmationUrl)
    {
        $additional = $userData['additional'];
        $country    = $additional['country'];
        $iso        = $country['countryiso'];

        $customerTokenModel = $this->customerTokenTransformer
            ->withUserData($userData)
            ->withMerchantUrls($confirmationUrl)
            ->toKlarnaModel($iso, $currency, 'de-DE', 'test', 'SUBSCRIPTION');

        /** @var array $normalizedOrder */
        $normalizedCustomerToken = $this->serializer->normalize($customerTokenModel);
        $request         = Request::createFromPayload($normalizedCustomerToken);
        $request->addQueryParameter('authorizationToken', $klarnaAuthToken);

        $this->customerTokenResource->create($request);
    }
}
