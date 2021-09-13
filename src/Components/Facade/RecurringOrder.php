<?php

declare(strict_types=1);

namespace BestitKlarnaOrderManagement\Components\Facade;

use BestitKlarnaOrderManagement\Components\Api\Request;
use BestitKlarnaOrderManagement\Components\Api\Resource\RecurringOrder as RecurringOrderResource;
use BestitKlarnaOrderManagement\Components\Api\Response;
use BestitKlarnaOrderManagement\Components\Shared\AuthorizationHelper;
use BestitKlarnaOrderManagement\Components\Shared\Localizer;
use BestitKlarnaOrderManagement\Components\Transformer\RecurringOrderTransformerInterface;
use Shopware\Models\Order\Order as SwOrder;
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

    public function __construct(
        RecurringOrderResource $recurringOrderResource,
        RecurringOrderTransformerInterface $recurringOrderTransformer,
        Serializer $serializer,
        Localizer $localizer,
        AuthorizationHelper $authorizationHelper
    )
    {
        $this->recurringOrderResource = $recurringOrderResource;
        $this->recurringOrderTransformer = $recurringOrderTransformer;
        $this->serializer = $serializer;
        $this->localizer = $localizer;
        $this->authorizationHelper = $authorizationHelper;
    }

    public function create(SwOrder $previousOrder, string $customerToken, array $orderBasket, array $userData, string $currency): Response
    {
        $recurringOrder = $this->recurringOrderTransformer->toKlarnaOrder($orderBasket, $userData, $currency, $this->localizer->localize(), $previousOrder->getInvoiceShippingTaxRate());

        $normalizedRecurringOrder = $this->serializer->normalize($recurringOrder);
        $request         = Request::createFromPayload($normalizedRecurringOrder);
        $request->addQueryParameter('customerToken', $customerToken);
        $this->authorizationHelper->setAuthHeader($request);

        return $this->recurringOrderResource->create($request);
    }
}
