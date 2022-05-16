<?php

declare(strict_types=1);

namespace BestitKlarnaOrderManagement\Components\Trigger;

use BestitKlarnaOrderManagement\Components\Api\Model\Error;
use BestitKlarnaOrderManagement\Components\Api\Response;
use BestitKlarnaOrderManagement\Components\Facade\Order as OrderFacade;
use BestitKlarnaOrderManagement\Components\PaymentInsights;
use BestitKlarnaOrderManagement\Components\Storage\DataProvider;
use Enlight_Components_Snippet_Manager;

/**
 * Takes care of the necessary actions that need to be done on the Klarna side when a payment method
 * changes in shopware.
 *
 * @author Senan Sharhan <senan.sharhan@bestit-online.de>
 */
class PaymentStatusChanged
{
    /** @var DataProvider $dataProvider */
    protected $dataProvider;
    /** @var OrderFacade $orderFacade */
    protected $orderFacade;
    /** @var paymentInsights $paymentInsights */
    protected $paymentInsights;
    /** @var Enlight_Components_Snippet_Manager $snippetManager */
    protected $snippetManager;

    public function __construct(
        DataProvider $dataProvider,
        OrderFacade $orderFacade,
        paymentInsights $paymentInsights,
        Enlight_Components_Snippet_Manager $snippetManager
    ) {
        $this->dataProvider    = $dataProvider;
        $this->orderFacade     = $orderFacade;
        $this->paymentInsights = $paymentInsights;
        $this->snippetManager  = $snippetManager;
    }

    /**
     * @param int $orderId
     * @param int $newPaymentId
     */
    public function execute($orderId, $newPaymentId): Response
    {
        $shopwareOrder = $this->dataProvider->getSwOrder($orderId);
        $oldPaymentId  = $shopwareOrder->getPayment()->getId();

        $paymentChanged = ($oldPaymentId != $newPaymentId);

        if (!$paymentChanged) {
            return Response::wrapEmptySuccessResponse();
        }

        $isKlarnaPayment = $this->paymentInsights->isKlarnaPaymentMethodId($newPaymentId);

        // if the new payment is also klarna payment a change will not be allowed
        if ($isKlarnaPayment) {
            $namespace    = $this->snippetManager->getNamespace('backend/orderManagement/Trigger/Payment');
            $errorMessage = $namespace->get(
                'CantChangeToKlarna',
                'you cant change Klarna payment to another Klarna Payment'
            );

            $error                = new Error();
            $error->errorMessages = [$errorMessage];

            return (new Response())->setError($error);
        }

        $klarnaOrderId = $shopwareOrder->getTransactionId();

        return $this->orderFacade->cancel($klarnaOrderId);
    }
}
