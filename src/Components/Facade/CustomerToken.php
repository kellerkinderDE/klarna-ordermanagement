<?php

declare(strict_types=1);

namespace BestitKlarnaOrderManagement\Components\Facade;

use BestitKlarnaOrderManagement\Components\Api\Request;
use BestitKlarnaOrderManagement\Components\Api\Resource\CustomerToken as CustomerTokenResource;
use BestitKlarnaOrderManagement\Components\Api\Response;
use BestitKlarnaOrderManagement\Components\Shared\Localizer;
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

    /** @var Localizer */
    private $localizer;

    public function __construct(
        CustomerTokenResource $customerTokenResource,
        CustomerTokenTransformerInterface $customerTokenTransformer,
        Serializer $serializer,
        Localizer $localizer
    )
    {
        $this->customerTokenResource = $customerTokenResource;
        $this->customerTokenTransformer = $customerTokenTransformer;
        $this->serializer = $serializer;
        $this->localizer = $localizer;
    }

    public function create(string $klarnaAuthToken, array $userData, array $orderBasket, string $confirmationUrl): Response
    {
        $additional = $userData['additional'];
        $country    = $additional['country'];
        $iso        = $country['countryiso'];
        $currency = $orderBasket['sCurrencyName'];

        $customerTokenModel = $this->customerTokenTransformer
            ->withUserData($userData)
            ->withMerchantUrls($confirmationUrl)
            ->toKlarnaModel($iso, $currency, $this->localizer->localize());

        /** @var array $normalizedOrder */
        $normalizedCustomerToken = $this->serializer->normalize($customerTokenModel);
        $request         = Request::createFromPayload($normalizedCustomerToken);
        $request->addQueryParameter('authorizationToken', $klarnaAuthToken);

        return $this->customerTokenResource->create($request);
    }
}
