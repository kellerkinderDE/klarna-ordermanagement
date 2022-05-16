<?php

declare(strict_types=1);

namespace BestitKlarnaOrderManagement\Components\Facade;

use BestitKlarnaOrderManagement\Components\Api\Request;
use BestitKlarnaOrderManagement\Components\Api\Resource\RecurringOrder as RecurringOrderResource;
use BestitKlarnaOrderManagement\Components\Api\Response;
use BestitKlarnaOrderManagement\Components\Shared\AuthorizationHelper;
use BestitKlarnaOrderManagement\Components\Shared\Localizer;
use BestitKlarnaOrderManagement\Components\Transformer\RecurringOrderTransformerInterface;
use Shopware\Models\Shop\Shop;
use Symfony\Component\Serializer\Serializer;

class RecurringOrder
{
    /** @var RecurringOrderResource */
    private $recurringOrderResource;

    /** @var RecurringOrderTransformerInterface */
    private $recurringOrderTransformer;

    /** @var Serializer */
    private $serializer;

    /** @var Localizer */
    private $localizer;

    /** @var AuthorizationHelper */
    private $authorizationHelper;

    /** @var Shop */
    private $shop;

    public function __construct(
        RecurringOrderResource $recurringOrderResource,
        RecurringOrderTransformerInterface $recurringOrderTransformer,
        Serializer $serializer,
        Localizer $localizer,
        AuthorizationHelper $authorizationHelper,
        Shop $shop
    ) {
        $this->recurringOrderResource    = $recurringOrderResource;
        $this->recurringOrderTransformer = $recurringOrderTransformer;
        $this->serializer                = $serializer;
        $this->localizer                 = $localizer;
        $this->authorizationHelper       = $authorizationHelper;
        $this->shop                      = $shop;
    }

    public function create(string $customerToken, array $orderBasket, array $userData): Response
    {
        $recurringOrder = $this->recurringOrderTransformer->toKlarnaOrder(
            $orderBasket,
            $userData,
            $this->shop->getCurrency()->getCurrency(),
            $this->localizer->localize()
        );

        $normalizedRecurringOrder = $this->serializer->normalize($recurringOrder);
        $request                  = Request::createFromPayload($normalizedRecurringOrder);
        $request->addQueryParameter('customerToken', $customerToken);
        $this->authorizationHelper->setAuthHeader($request);

        return $this->recurringOrderResource->create($request);
    }
}
