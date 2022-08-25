<?php

use BestitKlarnaOrderManagement\Components\Facade\OrderManagement as OrderManagementFacade;
use BestitKlarnaOrderManagement\Components\PaymentInsights;
use BestitKlarnaOrderManagement\Controllers\JsonableResponseTrait;
use Shopware\Components\CSRFWhitelistAware;

/**
 * Controller to show the Order Management functions for an order
 *
 * @author Senan Sharhan <senan.sharhan@bestit-online.de>
 */
class Shopware_Controllers_Backend_BestitOrderManagement extends Enlight_Controller_Action implements CSRFWhitelistAware
{
    use JsonableResponseTrait;

    /** @var Enlight_Controller_Plugins_ViewRenderer_Bootstrap */
    protected $viewRenderer;
    /** @var PaymentInsights */
    protected $paymentInsights;
    /** @var OrderManagementFacade */
    protected $orderManagementFacade;
    /** @var Enlight_Components_Snippet_Manager */
    protected $snippetManager;
    /** @var Enlight_Components_Snippet_Namespace */
    protected $nameSpace;

    /**
     * Configures the Controller
     *
     * Add the Order Management Template dir
     */
    public function preDispatch(): void
    {
        $this->get('template')->addTemplateDir($this->container->getParameter('bestit_order_management.template_dir'));
        $this->viewRenderer = $this->Front()->Plugins()->get('ViewRenderer');
        $this->viewRenderer->setNoRender();

        $this->orderManagementFacade = $this->get('bestit_klarna_order_management.components.facade.order_management');
        $this->paymentInsights       = $this->get('bestit_klarna_order_management.components.payment_insights');
        $this->snippetManager        = $this->get('snippets');
        $this->nameSpace             = $this->snippetManager->getNamespace('backend/bestitOrderManagement');
    }

    public function getWhitelistedCSRFActions(): array
    {
        return [
            'index',
            'createCapture',
            'createRefund',
            'resendCustomerCommunication',
            'extendAuthTime',
            'release',
            'releaseAndRefund',
            'cancelOrder',
            'isKlarnaOrder',
        ];
    }

    /**
     * Order Management overview
     */
    public function indexAction(): void
    {
        $this->viewRenderer->setNoRender(false);
        $shopwareOrderId = (int) $this->Request()->getParam('orderId');

        $this->orderManagementFacade->showKlarnaOrder($this->View(), $shopwareOrderId);

        try {
            $this->container->set('currency', new Zend_Currency($this->View()->getAssign('order')['purchase_currency']));
        } catch (\Exception $e) {
            // silent fail
        }
    }

    /**
     * Create a capture and return the new view
     */
    public function createCaptureAction()
    {
        $klarnaOrderId   = $this->Request()->getParam('order_id');
        $amount          = $this->Request()->getParam('amount');
        $lineItemsAsJson = $this->Request()->getParam('selectedLines');
        $description     = $this->Request()->getParam('description', '');

        if (empty($klarnaOrderId)) {
            return [
                'success'      => false,
                'errorMessage' => $this->nameSpace->get(
                    'CaptureFailed',
                    'Capture was not successful'
                ),
            ];
        }

        $response = $this->orderManagementFacade->captureOrder($klarnaOrderId, $amount, $lineItemsAsJson, $description);

        $this->jsonResponse($response);
    }

    /**
     * Create a refund and return the new view
     */
    public function createRefundAction()
    {
        $klarnaOrderId   = $this->Request()->getParam('order_id');
        $refundAmount    = $this->Request()->getParam('amount');
        $lineItemsAsJson = $this->Request()->getParam('selectedLines');
        $description     = $this->Request()->getParam('description', '');

        $errorMessage = $this->nameSpace->get(
            'RefundFailed',
            'Refund was not successful'
        );

        if (empty($klarnaOrderId)) {
            return [
                'success'      => false,
                'errorMessage' => $errorMessage,
            ];
        }

        $response = $this->orderManagementFacade->refundOrder(
            $klarnaOrderId,
            $refundAmount,
            $lineItemsAsJson,
            $description
        );

        $this->jsonResponse($response);
    }

    /**
     * Trigger resend of customer communication
     */
    public function resendCustomerCommunicationAction(): void
    {
        $klarnaOrderId = $this->Request()->getParam('order_id');
        $captureId     = $this->Request()->getParam('capture_id');
        $response      = $this->orderManagementFacade->resendCustomerCommunication($klarnaOrderId, $captureId);

        $this->jsonResponse($response);
    }

    /**
     * Extend authorization time
     */
    public function extendAuthTimeAction(): void
    {
        $klarnaOrderId = $this->Request()->getParam('order_id');
        $response      = $this->orderManagementFacade->extendAuthTime($klarnaOrderId);

        $this->jsonResponse($response);
    }

    /**
     * Release remaining authorization
     */
    public function releaseAction(): void
    {
        $klarnaOrderId = $this->Request()->getParam('order_id');
        $response      = $this->orderManagementFacade->releaseRemainingAmount($klarnaOrderId);

        $this->jsonResponse($response);
    }

    /**
     * Cancel Order
     */
    public function cancelOrderAction(): void
    {
        $klarnaOrderId = $this->Request()->getParam('order_id');
        $response      = $this->orderManagementFacade->cancelOrder($klarnaOrderId);

        $this->jsonResponse($response);
    }

    /**
     * Check if the order is a klarna order or not
     */
    public function isKlarnaOrderAction(): void
    {
        $paymentId = (int) $this->Request()->getParam('paymentId');

        $this->jsonResponse([
            'success' => $this->paymentInsights->isKlarnaPaymentMethodId($paymentId),
        ]);
    }
}
