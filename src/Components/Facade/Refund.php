<?php

declare(strict_types=1);

namespace BestitKlarnaOrderManagement\Components\Facade;

use BestitKlarnaOrderManagement\Components\Api\Model\LineItem;
use BestitKlarnaOrderManagement\Components\Api\Model\Refund as RefundModel;
use BestitKlarnaOrderManagement\Components\Api\Request;
use BestitKlarnaOrderManagement\Components\Api\Resource\Refund as RefundResource;
use BestitKlarnaOrderManagement\Components\Api\Response;
use BestitKlarnaOrderManagement\Components\Logging\TransactionLoggerInterface;
use BestitKlarnaOrderManagement\Components\Shared\AuthorizationHelper;
use BestitKlarnaOrderManagement\Components\Storage\DataWriter;
use Shopware\Models\Order\Status;
use Symfony\Component\Serializer\Serializer;

/**
 * @author Senan Sharhan <senan.sharhan@bestit-online.de>
 */
class Refund
{
    /** @var RefundResource */
    protected $refundResource;
    /** @var Serializer */
    protected $serializer;
    /** @var DataWriter */
    protected $dataWriter;
    /** @var TransactionLoggerInterface */
    protected $transactionLogger;
    /** @var AuthorizationHelper */
    protected $authorizationHelper;

    public function __construct(
        RefundResource $refundResource,
        Serializer $serializer,
        DataWriter $dataWriter,
        TransactionLoggerInterface $transactionLogger,
        AuthorizationHelper $authorizationHelper
    ) {
        $this->refundResource      = $refundResource;
        $this->serializer          = $serializer;
        $this->dataWriter          = $dataWriter;
        $this->transactionLogger   = $transactionLogger;
        $this->authorizationHelper = $authorizationHelper;
    }

    /**
     * @param string            $orderId
     * @param int               $refundAmount
     * @param null|array|string $lineItems
     * @param null|string       $description
     *
     * @deprecated Passing $lineItems as a JSON string is deprecated and will be removed in 2.0.
     *             You should pass an array of LineItem objects instead.
     */
    public function create($orderId, $refundAmount, $lineItems = null, $description = null): Response
    {
        $refund = new RefundModel();

        $refund->refundedAmount = $refundAmount;
        $refund->description    = $description;

        if ($lineItems !== null) {
            if (is_array($lineItems)) {
                $refund->orderLines = $lineItems;
            } else {
                $refund->orderLines = $this->serializer->deserialize($lineItems, LineItem::class . '[]', 'json');
            }
        }

        $request = Request::createFromPayload($this->serializer->normalize($refund))
            ->addQueryParameter('order_id', $orderId);
        $this->authorizationHelper->setAuthHeader($request);

        $response = $this->refundResource->create($request);

        $this->transactionLogger->createRefund($request, $response);

        if ($response->isError()) {
            $this->dataWriter->updatePaymentStatus($orderId, Status::PAYMENT_STATE_REVIEW_NECESSARY);

            return $response;
        }

        $this->dataWriter->updatePaymentStatus($orderId, Status::PAYMENT_STATE_RE_CREDITING);

        return $response;
    }
}
