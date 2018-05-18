<?php

namespace BestitKlarnaOrderManagement\Components\Facade;

use BestitKlarnaOrderManagement\Components\Api\Model\Capture as CaptureModel;
use BestitKlarnaOrderManagement\Components\Api\Model\LineItem;
use BestitKlarnaOrderManagement\Components\Api\Model\Order as KlarnaOrder;
use BestitKlarnaOrderManagement\Components\Api\Model\ShippingInfo;
use BestitKlarnaOrderManagement\Components\Api\Request;
use BestitKlarnaOrderManagement\Components\Api\Resource\Capture as CaptureResource;
use BestitKlarnaOrderManagement\Components\Api\Response;
use BestitKlarnaOrderManagement\Components\Facade\Order as OrderFacade;
use BestitKlarnaOrderManagement\Components\Logging\TransactionLoggerInterface;
use BestitKlarnaOrderManagement\Components\Storage\DataProvider;
use BestitKlarnaOrderManagement\Components\Storage\DataWriter;
use Shopware\Models\Order\Status;
use Symfony\Component\Serializer\Serializer;

/**
 * Facade to interact with Klarna capture(s).
 *
 * @package BestitKlarnaOrderManagement\Components\Facade
 *
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 * @author Senan Sharhan <senan.sharhan@bestit-online.de>
 */
class Capture
{
    /** @var Order */
    protected $orderFacade;
    /** @var CaptureResource */
    protected $captureResource;
    /** @var Serializer */
    protected $serializer;
    /** @var DataProvider */
    protected $dataProvider;
    /** @var DataWriter */
    protected $dataWriter;
    /** @var TransactionLoggerInterface */
    protected $transactionLogger;

    /**
     * @param Order                      $orderFacade
     * @param CaptureResource            $captureResource
     * @param Serializer                 $serializer
     * @param DataProvider               $dataProvider
     * @param DataWriter                 $dataWriter
     * @param TransactionLoggerInterface $transactionLogger
     */
    public function __construct(
        OrderFacade $orderFacade,
        CaptureResource $captureResource,
        Serializer $serializer,
        DataProvider $dataProvider,
        DataWriter $dataWriter,
        TransactionLoggerInterface $transactionLogger
    ) {
        $this->orderFacade = $orderFacade;
        $this->captureResource = $captureResource;
        $this->serializer = $serializer;
        $this->dataProvider = $dataProvider;
        $this->dataWriter = $dataWriter;
        $this->transactionLogger = $transactionLogger;
    }

    /**
     * @param string              $orderId
     * @param int                 $amount
     * @param string|null         $lineItemsAsJson
     * @param string|null         $description
     * @param ShippingInfo[]|null $shippingInfo
     * @return Response
     */
    public function create($orderId, $amount, $lineItemsAsJson = null, $description = null, array $shippingInfo = null)
    {
        $capture = new CaptureModel();

        $capture->capturedAmount = $amount;
        $capture->description = $description;

        if ($shippingInfo === null) {
            $trackingInfo = $this->dataProvider->getTrackingInfo($orderId);
            $trackingCode = isset($trackingInfo['trackingCode']) ? $trackingInfo['trackingCode'] : null;
            $dispatchName = isset($trackingInfo['dispatchName']) ? $trackingInfo['dispatchName'] : null;

            if ($trackingCode !== null) {
                $shippingInfoModel = new ShippingInfo();
                $shippingInfoModel->trackingNumber = $trackingCode;
                $shippingInfoModel->shippingCompany = $dispatchName;
                $shippingInfo = [$shippingInfoModel];
            }
        }

        $capture->shippingInfo = $shippingInfo;

        if ($lineItemsAsJson !== null) {
            $capture->orderLines = $this->serializer->deserialize($lineItemsAsJson, LineItem::class . '[]', 'json');
        }

        $request = Request::createFromPayload($this->serializer->normalize($capture))
            ->addQueryParameter('order_id', $orderId);

        $response = $this->captureResource->create($request);
        $this->transactionLogger->createCapture($request, $response);

        if ($response->isError()) {
            $this->dataWriter->updatePaymentStatus($orderId, Status::PAYMENT_STATE_REVIEW_NECESSARY);

            return $response;
        }

        $orderResponse = $this->orderFacade->get($orderId);

        if ($orderResponse->isError()) {
            return $response;
        }

        /** @var KlarnaOrder $order */
        $order = $orderResponse->getResponseObject();

        $paymentStatus = $order->remainingAuthorizedAmount == 0 ?
            Status::PAYMENT_STATE_COMPLETELY_PAID :
            Status::PAYMENT_STATE_PARTIALLY_PAID;

        $this->dataWriter->updatePaymentStatus($orderId, $paymentStatus);

        return $response;
    }

    /**
     * @param string $orderId
     * @param string $captureId
     *
     * @return Response
     */
    public function resend($orderId, $captureId)
    {
        $request = new Request();
        $request->addQueryParameter('order_id', $orderId);
        $request->addQueryParameter('capture_id', $captureId);

        return $this->captureResource->resend($request);
    }

    /**
     * @param string $orderId
     * @param string $captureId
     * @param string $trackingNumber
     * @param string $shippingCompany
     *
     * @return Response
     */
    public function updateShippingInfo($orderId, $captureId, $trackingNumber, $shippingCompany)
    {
        $shippingInfoModel = new ShippingInfo();
        $shippingInfoModel->trackingNumber = $trackingNumber;
        $shippingInfoModel->shippingCompany = $shippingCompany;

        $request = Request::createFromPayload([
            'shipping_info' => $this->serializer->normalize([$shippingInfoModel])
        ])->addQueryParameter('order_id', $orderId)->addQueryParameter('capture_id', $captureId);

        return $this->captureResource->updateShippingInfo($request);
    }
}
