<?php

namespace BestitKlarnaOrderManagement\Components\Facade;

use BestitKlarnaOrderManagement\Components\Api\Model\BillingAddress;
use BestitKlarnaOrderManagement\Components\Api\Model\LineItem;
use BestitKlarnaOrderManagement\Components\Api\Model\ShippingAddress;
use BestitKlarnaOrderManagement\Components\Api\Request;
use BestitKlarnaOrderManagement\Components\Api\Resource\Order as OrderResource;
use BestitKlarnaOrderManagement\Components\Api\Response;
use BestitKlarnaOrderManagement\Components\Logging\TransactionLoggerInterface;
use BestitKlarnaOrderManagement\Components\Storage\DataWriter;
use Shopware\Models\Order\Status;
use Symfony\Component\Serializer\Serializer;

/**
 * Interface to interact with Klarna order(s).
 *
 * @package BestitKlarnaOrderManagement\Components\Facade
 *
 * @author Senan Sharhan <senan.sharhan@bestit-online.de>
 */
class Order
{
    /** @var OrderResource */
    protected $orderResource;
    /** @var Serializer */
    protected $serializer;
    /** @var DataWriter */
    protected $dataWriter;
    /** @var TransactionLoggerInterface */
    protected $transactionLogger;

    /**
     * @param OrderResource              $orderResource
     * @param Serializer                 $serializer
     * @param DataWriter                 $dataWriter
     * @param TransactionLoggerInterface $transactionLogger
     */
    public function __construct(
        OrderResource $orderResource,
        Serializer $serializer,
        DataWriter $dataWriter,
        TransactionLoggerInterface $transactionLogger
    ) {
        $this->orderResource = $orderResource;
        $this->serializer = $serializer;
        $this->dataWriter = $dataWriter;
        $this->transactionLogger = $transactionLogger;
    }

    /**
     * @param string $orderId
     *
     * @return Response
     */
    public function get($orderId)
    {
        $request = new Request();
        $request->addQueryParameter('order_id', $orderId);

        return $this->orderResource->get($request);
    }

    /**
     * @param string $orderId
     *
     * @return Response
     */
    public function extendAuthTime($orderId)
    {
        $request = new Request();
        $request->addQueryParameter('order_id', $orderId);

        $response = $this->orderResource->extendAuthTime($request);

        $this->transactionLogger->extendAuthTime($request, $response);

        return $response;
    }

    /**
     * @param string $orderId
     *
     * @return Response
     */
    public function releaseRemainingAmount($orderId)
    {
        $request = new Request();
        $request->addQueryParameter('order_id', $orderId);

        $response = $this->orderResource->releaseRemainingAmount($request);

        $this->transactionLogger->releaseRemainingAmount($request, $response);

        return $response;
    }

    /**
     * @param string $orderId
     *
     * @return Response
     */
    public function cancel($orderId)
    {
        $request = new Request();
        $request->addQueryParameter('order_id', $orderId);

        $response = $this->orderResource->cancel($request);

        $this->transactionLogger->cancelOrder($request, $response);

        if ($response->isError()) {
            $this->dataWriter->updatePaymentStatus($orderId, Status::PAYMENT_STATE_REVIEW_NECESSARY);

            return $response;
        }

        $this->dataWriter->updatePaymentStatus($orderId, Status::PAYMENT_STATE_THE_PROCESS_HAS_BEEN_CANCELLED);

        return $response;
    }

    /**
     * @param string          $orderId
     * @param ShippingAddress $shipping
     * @param BillingAddress  $billing
     *
     * @return Response
     */
    public function updateAddresses($orderId, ShippingAddress $shipping, BillingAddress $billing)
    {
        $request = Request::createFromPayload([
            'shipping_address' => $this->serializer->normalize($shipping),
            'billing_address' => $this->serializer->normalize($billing)
        ])->addQueryParameter('order_id', $orderId);

        return $this->orderResource->updateAddress($request);
    }

    /**
     * @param  int       $orderId
     * @param  int       $orderAmount
     * @param LineItem[] $newLineItem
     *
     * @return Response
     */
    public function updateOrder($orderId, $orderAmount, $newLineItem)
    {
        $request = Request::createFromPayload([
            'order_amount' => $orderAmount,
            'order_lines' => $this->serializer->normalize($newLineItem),
        ])->addQueryParameter('order_id', $orderId);

        $response = $this->orderResource->updateOrder($request);

        $this->transactionLogger->updateOrder($request, $response);

        return $response;
    }

    /**
     * @param string $orderId
     *
     * @return Response
     */
    public function acknowledge($orderId)
    {
        $request = new Request();
        $request->addQueryParameter('order_id', $orderId);

        return $this->orderResource->acknowledge($request);
    }
}
