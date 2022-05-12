<?php

namespace BestitKlarnaOrderManagement\Components\Facade;

use BestitKlarnaOrderManagement\Components\Api\Model\Capture as CaptureModel;
use BestitKlarnaOrderManagement\Components\Api\Model\LineItem;
use BestitKlarnaOrderManagement\Components\Api\Model\Order as KlarnaOrder;
use BestitKlarnaOrderManagement\Components\Api\Model\ShippingInfo;
use BestitKlarnaOrderManagement\Components\Api\Request;
use BestitKlarnaOrderManagement\Components\Api\Resource\Capture as CaptureResource;
use BestitKlarnaOrderManagement\Components\Api\Response;
use BestitKlarnaOrderManagement\Components\ConfigReader;
use BestitKlarnaOrderManagement\Components\Facade\Order as OrderFacade;
use BestitKlarnaOrderManagement\Components\Logging\TransactionLoggerInterface;
use BestitKlarnaOrderManagement\Components\Shared\AuthorizationHelper;
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
    /** @var AuthorizationHelper */
    protected $authorizationHelper;
    /** @var configReader */
    protected $configReader;

    /**
     * @param Order                      $orderFacade
     * @param CaptureResource            $captureResource
     * @param Serializer                 $serializer
     * @param DataProvider               $dataProvider
     * @param DataWriter                 $dataWriter
     * @param TransactionLoggerInterface $transactionLogger
     * @param AuthorizationHelper        $authorizationHelper
     * @param ConfigReader               $configReader
     */
    public function __construct(
        OrderFacade $orderFacade,
        CaptureResource $captureResource,
        Serializer $serializer,
        DataProvider $dataProvider,
        DataWriter $dataWriter,
        TransactionLoggerInterface $transactionLogger,
        AuthorizationHelper $authorizationHelper,
        ConfigReader $configReader
    ) {
        $this->orderFacade = $orderFacade;
        $this->captureResource = $captureResource;
        $this->serializer = $serializer;
        $this->dataProvider = $dataProvider;
        $this->dataWriter = $dataWriter;
        $this->transactionLogger = $transactionLogger;
        $this->authorizationHelper = $authorizationHelper;
        $this->configReader = $configReader;
    }

    /**
     * @param string              $orderId
     * @param int                 $amount
     * @param string|array|null   $lineItems
     * @param string|null         $description
     * @param ShippingInfo[]|null $shippingInfo
     * @return Response
     *
     * @deprecated Passing $lineItems as a JSON string is deprecated and will be removed in 2.0.
     *             You should pass an array of LineItem objects instead.
     */
    public function create($orderId, $amount, $lineItems = null, $description = null, array $shippingInfo = null)
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


        if ($lineItems !== null) {
            if (is_array($lineItems)) {
                $capture->orderLines = $lineItems;
            } else {
                $capture->orderLines = $this->serializer->deserialize($lineItems, LineItem::class . '[]', 'json');
            }
        }

        $request = Request::createFromPayload($this->serializer->normalize($capture))
            ->addQueryParameter('order_id', $orderId);
        $this->authorizationHelper->setAuthHeader($request);

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

        $paymentStatus = $order->remainingAuthorizedAmount === 0 ?
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
        $this->authorizationHelper->setAuthHeader($request);

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
        $delimitedTrackingNumber = $this->splitShipmentNumbers($trackingNumber);
        $shippingInfoModels = [];

        foreach ($delimitedTrackingNumber as $value) {
            $shippingInfoModel = new ShippingInfo();
            $shippingInfoModel->trackingNumber = $value;
            $shippingInfoModel->shippingCompany = $shippingCompany;
            $shippingInfoModels[] = $shippingInfoModel;
        }

        $request = Request::createFromPayload([
            'shipping_info' => $this->serializer->normalize($shippingInfoModels)
        ])->addQueryParameter('order_id', $orderId)->addQueryParameter('capture_id', $captureId);

        $this->authorizationHelper->setAuthHeader($request);

        return $this->captureResource->updateShippingInfo($request);
    }

    private function splitShipmentNumbers(string $trackingCodes): array
    {
        $delimiter = (string) $this->configReader->get('trackingnumber_delimiter');

        return explode($delimiter, $trackingCodes);
    }
}
