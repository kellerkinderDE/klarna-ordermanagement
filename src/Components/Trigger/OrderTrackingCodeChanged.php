<?php

namespace BestitKlarnaOrderManagement\Components\Trigger;

use BestitKlarnaOrderManagement\Components\Api\Model\Order as KlarnaOrderModel;
use BestitKlarnaOrderManagement\Components\Api\Response;
use BestitKlarnaOrderManagement\Components\Facade\Capture as CaptureFacade;
use BestitKlarnaOrderManagement\Components\Facade\Order as OrderFacade;
use BestitKlarnaOrderManagement\Components\Storage\DataProvider;

/**
 * Synchronizes the tracking code changes with Klarna.
 *
 * @author  Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 * @author Senan Sharhan <senan.sharhan@bestit-online.de>
 */
class OrderTrackingCodeChanged
{
    /** @var DataProvider */
    protected $dataProvider;
    /** @var OrderFacade */
    protected $orderFacade;
    /** @var CaptureFacade */
    protected $captureFacade;

    public function __construct(
        OrderFacade $orderFacade,
        CaptureFacade $captureFacade,
        DataProvider $dataProvider
    ) {
        $this->dataProvider  = $dataProvider;
        $this->orderFacade   = $orderFacade;
        $this->captureFacade = $captureFacade;
    }

    /**
     * @param int    $swOrderId
     * @param string $trackingCode
     */
    public function execute($swOrderId, $trackingCode): Response
    {
        $klarnaOrderId = $this->dataProvider->getKlarnaOrderId($swOrderId);
        $trackingInfo  = $this->dataProvider->getTrackingInfo($klarnaOrderId);

        $oldTrackingCode = $trackingInfo['trackingCode'] ?? null;
        $dispatchName    = $trackingInfo['dispatchName'] ?? null;

        if ($oldTrackingCode === $trackingCode) {
            return Response::wrapEmptySuccessResponse();
        }

        $klarnaOrderResponse = $this->orderFacade->get($klarnaOrderId);

        if ($klarnaOrderResponse->isError()) {
            return $klarnaOrderResponse;
        }

        /** @var KlarnaOrderModel $klarnaOrder */
        $klarnaOrder = $klarnaOrderResponse->getResponseObject();

        foreach ($klarnaOrder->captures as $capture) {
            $updateShippingInfoResponse = $this->captureFacade->updateShippingInfo(
                $klarnaOrderId,
                $capture['capture_id'],
                $trackingCode,
                $dispatchName
            );

            if ($updateShippingInfoResponse->isError()) {
                return $updateShippingInfoResponse;
            }
        }

        return Response::wrapEmptySuccessResponse();
    }
}
