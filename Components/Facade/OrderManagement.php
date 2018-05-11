<?php

namespace BestitKlarnaOrderManagement\Components\Facade;

use BestitKlarnaOrderManagement\Components\Calculator\CalculatorInterface;
use BestitKlarnaOrderManagement\Components\DataProvider\DataProvider;
use BestitKlarnaOrderManagement\Components\Facade\Order as OrderFacade;
use BestitKlarnaOrderManagement\Components\Facade\Capture as CaptureFacade;
use BestitKlarnaOrderManagement\Components\Facade\Refund as RefundFacade;
use Enlight_Components_Snippet_Manager;
use Enlight_Components_Snippet_Namespace;
use Enlight_View_Default;

class OrderManagement
{
    /** @var DataProvider */
    protected $dataProvider;
    /** @var OrderFacade */
    protected $orderFacade;
    /** @var CaptureFacade */
    protected $captureFacade;
    /** @var RefundFacade */
    protected $refundFacade;
    /** @var CalculatorInterface */
    protected $calculator;
    /** @var Enlight_Components_Snippet_Namespace */
    protected $nameSpace;

    /**
     * @param DataProvider                       $dataProvider
     * @param Order                              $orderFacade
     * @param Capture                            $captureFacade
     * @param Refund                             $refundFacade
     * @param CalculatorInterface                $calculator
     * @param Enlight_Components_Snippet_Manager $snippetManager
     */
    public function __construct(
        DataProvider $dataProvider,
        OrderFacade $orderFacade,
        CaptureFacade $captureFacade,
        RefundFacade $refundFacade,
        CalculatorInterface $calculator,
        Enlight_Components_Snippet_Manager $snippetManager
    ) {
        $this->dataProvider = $dataProvider;
        $this->orderFacade = $orderFacade;
        $this->captureFacade = $captureFacade;
        $this->refundFacade = $refundFacade;
        $this->calculator = $calculator;
        $this->nameSpace = $snippetManager->getNamespace('backend/orderManagement');
    }

    /**
     * @param Enlight_View_Default $view
     * @param int                  $shopwareOrderId
     */
    public function showKlarnaOrder(Enlight_View_Default $view, $shopwareOrderId)
    {
        $order = $this->dataProvider->getSwOrder($shopwareOrderId);
        $klarnaOrderId = $order->getTransactionId();

        $response = $this->orderFacade->get($klarnaOrderId);

        $view->assign([
            'order' => json_decode($response->getRawResponse(), true),
            'success' => $response->isSuccessful(),
            'klarna_id' => $klarnaOrderId,
            'error' => $response->getError(),
            'logs' => $this->dataProvider->getLogs($klarnaOrderId)
        ]);
    }

    /**
     * @param string      $klarnaOrderId
     * @param float       $amount
     * @param string|null $lineItemsAsJson
     * @param string|null $description
     *
     * @return array
     */
    public function captureOrder($klarnaOrderId, $amount, $lineItemsAsJson, $description)
    {
        $amountInCents = $this->calculator->toCents($amount);
        $lineItemsAsJson = $lineItemsAsJson ?: null;
        $description = trim($description) ?: null;

        $response = $this->captureFacade->create(
            $klarnaOrderId,
            $amountInCents,
            $lineItemsAsJson,
            $description
        );

        return [
            'success' => $response->isSuccessful(),
            'message' => $this->nameSpace->get('CaptureSuccess', 'Capture was successfully'),
            'errorMessage' => $response->getError()->errorMessages
        ];
    }

    /**
     * @param string      $klarnaOrderId
     * @param float       $refundAmount
     * @param string|null $lineItemsAsJson
     * @param string|null $description
     *
     * @return array
     */
    public function refundOrder($klarnaOrderId, $refundAmount, $lineItemsAsJson, $description)
    {
        $refundAmountInCents = $this->calculator->toCents($refundAmount);
        $lineItemsAsJson = $lineItemsAsJson ?: null;
        $description = trim($description) ?: null;

        $response = $this->refundFacade->create($klarnaOrderId, $refundAmountInCents, $lineItemsAsJson, $description);

        return [
            'success' => $response->isSuccessful(),
            'message' => $this->nameSpace->get('RefundSuccess', 'Refund was successfully'),
            'errorMessage' => $response->getError()->errorMessages
        ];
    }

    /**
     * @param string $klarnaOrderId
     * @param string $captureId
     *
     * @return array
     */
    public function resendCustomerCommunication($klarnaOrderId, $captureId)
    {
        $response = $this->captureFacade->resend($klarnaOrderId, $captureId);

        return [
            'success' => $response->isSuccessful(),
            'message' => $this->nameSpace->get(
                'resendCustomerCommunicationSuccess',
                'Customer Communication has been sent successfully'
            ),
            'errorMessage' => $response->getError()->errorMessages
        ];
    }

    /**
     * @param string $klarnaOrderId
     *
     * @return array
     */
    public function extendAuthTime($klarnaOrderId)
    {
        $response = $this->orderFacade->extendAuthTime($klarnaOrderId);

        return [
            'success' => $response->isSuccessful(),
            'message' => $this->nameSpace->get('extendAuthTimeSuccess', 'Authorization time has been extended'),
            'errorMessage' => $response->getError()->errorMessages
        ];
    }

    /**
     * @param string $klarnaOrderId
     *
     * @return array
     */
    public function releaseRemainingAmount($klarnaOrderId)
    {
        $response = $this->orderFacade->releaseRemainingAmount($klarnaOrderId);

        return [
            'success' => $response->isSuccessful(),
            'message' => $this->nameSpace->get('releaseSuccess', 'Remaining amount has been released'),
            'errorMessage' => $response->getError()->errorMessages
        ];
    }

    /**
     * @param string $klarnaOrderId
     *
     * @return array
     */
    public function cancelOrder($klarnaOrderId)
    {
        $response = $this->orderFacade->cancel($klarnaOrderId);

        return [
            'success' => $response->isSuccessful(),
            'message' => $this->nameSpace->get('cancelSuccess', 'Order has been canceled'),
            'errorMessage' => $response->getError()->errorMessages
        ];
    }
}
