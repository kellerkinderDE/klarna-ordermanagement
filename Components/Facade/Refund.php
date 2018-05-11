<?php

namespace BestitKlarnaOrderManagement\Components\Facade;

use BestitKlarnaOrderManagement\Components\Api\Model\LineItem;
use BestitKlarnaOrderManagement\Components\Api\Model\Refund as RefundModel;
use BestitKlarnaOrderManagement\Components\Api\Resource\Refund as RefundResource;
use BestitKlarnaOrderManagement\Components\Api\Response;
use BestitKlarnaOrderManagement\Components\DataWriter;
use BestitKlarnaOrderManagement\Components\Logging\TransactionLoggerInterface;
use Shopware\Models\Order\Status;
use Symfony\Component\Serializer\Serializer;
use BestitKlarnaOrderManagement\Components\Api\Request;

/**
 * Interface to interact with Klarna refund(s).
 *
 * @package BestitKlarnaOrderManagement\Components\Facade
 *
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

    /**
     * @param RefundResource             $refundResource
     * @param Serializer                 $serializer
     * @param DataWriter                 $dataWriter
     * @param TransactionLoggerInterface $transactionLogger
     */
    public function __construct(
        RefundResource $refundResource,
        Serializer $serializer,
        DataWriter $dataWriter,
        TransactionLoggerInterface $transactionLogger
    ) {
        $this->refundResource = $refundResource;
        $this->serializer = $serializer;
        $this->dataWriter = $dataWriter;
        $this->transactionLogger = $transactionLogger;
    }

    /**
     * @param string        $orderId
     * @param int           $refundAmount
     * @param string|null   $lineItemsAsJson
     * @param string|null   $description
     *
     * @return Response
     */
    public function create($orderId, $refundAmount, $lineItemsAsJson = null, $description = null)
    {
        $refund = new RefundModel();

        $refund->refundedAmount = $refundAmount;
        $refund->description = $description;
        if ($lineItemsAsJson !== null) {
            $refund->orderLines = $this->serializer->deserialize($lineItemsAsJson, LineItem::class . '[]', 'json');
        }

        $request = Request::createFromPayload($this->serializer->normalize($refund))
            ->addQueryParameter('order_id', $orderId)
        ;

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
