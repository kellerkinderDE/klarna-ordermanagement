<?php

namespace BestitKlarnaOrderManagement\Components\Facade;

use BestitKlarnaOrderManagement\Components\Api\Model\BillingAddress;
use BestitKlarnaOrderManagement\Components\Api\Model\LineItem;
use BestitKlarnaOrderManagement\Components\Api\Model\MerchantReferences;
use BestitKlarnaOrderManagement\Components\Api\Model\ShippingAddress;
use BestitKlarnaOrderManagement\Components\Api\Request;
use BestitKlarnaOrderManagement\Components\Api\Resource\Order as OrderResource;
use BestitKlarnaOrderManagement\Components\Api\Response;
use BestitKlarnaOrderManagement\Components\Logging\TransactionLoggerInterface;
use BestitKlarnaOrderManagement\Components\Shared\AuthorizationHelper;
use BestitKlarnaOrderManagement\Components\Storage\DataWriter;
use Shopware\Models\Order\Status;
use Symfony\Component\Serializer\Serializer;

/**
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
    /** @var AuthorizationHelper */
    protected $authorizationHelper;

    public function __construct(
        OrderResource $orderResource,
        Serializer $serializer,
        DataWriter $dataWriter,
        TransactionLoggerInterface $transactionLogger,
        AuthorizationHelper $authorizationHelper
    ) {
        $this->orderResource       = $orderResource;
        $this->serializer          = $serializer;
        $this->dataWriter          = $dataWriter;
        $this->transactionLogger   = $transactionLogger;
        $this->authorizationHelper = $authorizationHelper;
    }

    /**
     * @param string $orderId
     */
    public function get($orderId): Response
    {
        $request = new Request();
        $request->addQueryParameter('order_id', $orderId);
        $this->authorizationHelper->setAuthHeader($request);

        return $this->orderResource->get($request);
    }

    /**
     * @param string $orderId
     */
    public function extendAuthTime($orderId): Response
    {
        $request = new Request();
        $request->addQueryParameter('order_id', $orderId);
        $this->authorizationHelper->setAuthHeader($request);

        $response = $this->orderResource->extendAuthTime($request);

        $this->transactionLogger->extendAuthTime($request, $response);

        return $response;
    }

    /**
     * @param string $orderId
     */
    public function releaseRemainingAmount($orderId): Response
    {
        $request = new Request();
        $request->addQueryParameter('order_id', $orderId);
        $this->authorizationHelper->setAuthHeader($request);

        $response = $this->orderResource->releaseRemainingAmount($request);

        $this->transactionLogger->releaseRemainingAmount($request, $response);

        return $response;
    }

    /**
     * @param string $orderId
     * @param bool   $updatePaymentStatus
     */
    public function cancel($orderId, $updatePaymentStatus = true): Response
    {
        $request = new Request();
        $request->addQueryParameter('order_id', $orderId);
        $this->authorizationHelper->setAuthHeader($request);

        $response = $this->orderResource->cancel($request);

        $this->transactionLogger->cancelOrder($request, $response);

        if ($updatePaymentStatus) {
            if ($response->isError()) {
                $this->dataWriter->updatePaymentStatus($orderId, Status::PAYMENT_STATE_REVIEW_NECESSARY);

                return $response;
            }

            $this->dataWriter->updatePaymentStatus($orderId, Status::PAYMENT_STATE_THE_PROCESS_HAS_BEEN_CANCELLED);
        }

        return $response;
    }

    /**
     * @param string $orderId
     */
    public function updateAddresses($orderId, ShippingAddress $shipping, BillingAddress $billing): Response
    {
        $request = Request::createFromPayload([
            'shipping_address' => $this->serializer->normalize($shipping),
            'billing_address'  => $this->serializer->normalize($billing),
        ])->addQueryParameter('order_id', $orderId);
        $this->authorizationHelper->setAuthHeader($request);

        return $this->orderResource->updateAddress($request);
    }

    /**
     * @param string     $orderId
     * @param int        $orderAmount
     * @param LineItem[] $newLineItem
     */
    public function updateOrder($orderId, $orderAmount, $newLineItem): Response
    {
        $request = Request::createFromPayload([
            'order_amount' => $orderAmount,
            'order_lines'  => $this->serializer->normalize($newLineItem),
        ])->addQueryParameter('order_id', $orderId);
        $this->authorizationHelper->setAuthHeader($request);

        $response = $this->orderResource->updateOrder($request);

        $this->transactionLogger->updateOrder($request, $response);

        return $response;
    }

    /**
     * @param string $orderId
     */
    public function acknowledge($orderId): Response
    {
        $request = new Request();
        $request->addQueryParameter('order_id', $orderId);
        $this->authorizationHelper->setAuthHeader($request);

        return $this->orderResource->acknowledge($request);
    }

    /**
     * @param string $orderId
     */
    public function updateMerchantReferences($orderId, MerchantReferences $merchantReferences): Response
    {
        $request = Request::createFromPayload(
            $this->serializer->normalize($merchantReferences)
        )->addQueryParameter('order_id', $orderId);
        $this->authorizationHelper->setAuthHeader($request);

        return $this->orderResource->updateMerchantReferences($request);
    }
}
