<?php

declare(strict_types=1);

namespace BestitKlarnaOrderManagement\Subscriber\Controller\Backend;

use BestitKlarnaOrderManagement\Components\ConfigReader;
use BestitKlarnaOrderManagement\Components\Exception\NoOrderFoundException;
use BestitKlarnaOrderManagement\Components\PaymentInsights;
use BestitKlarnaOrderManagement\Components\Shared\ShopwareVersionHelper;
use BestitKlarnaOrderManagement\Components\Storage\DataProvider;
use BestitKlarnaOrderManagement\Components\Trigger\AddressChanged as AddressChangedTrigger;
use BestitKlarnaOrderManagement\Components\Trigger\LineItemAdded as LineItemAddedTrigger;
use BestitKlarnaOrderManagement\Components\Trigger\LineItemChanged as LineItemChangedTrigger;
use BestitKlarnaOrderManagement\Components\Trigger\LineItemDeleted as LineItemDeletedTrigger;
use BestitKlarnaOrderManagement\Components\Trigger\OrderDeleted as OrderDeletedTrigger;
use BestitKlarnaOrderManagement\Components\Trigger\OrderTrackingCodeChanged as OrderTrackingCodeChangedTrigger;
use BestitKlarnaOrderManagement\Components\Trigger\PaymentStatusChanged as PaymentStatusChangedTrigger;
use Enlight\Event\SubscriberInterface;
use Enlight_Controller_Action;
use Enlight_Controller_ActionEventArgs;
use Enlight_Hook_HookArgs;
use Shopware;

/**
 * Subscribers for the backend order page(s).
 *
 * @author Senan Sharhan <senan.sharhan@bestit-online.de>
 */
class Order implements SubscriberInterface
{
    /** @var AddressChangedTrigger */
    protected $addressChangedTrigger;
    /** @var OrderDeletedTrigger */
    protected $orderDeletedTrigger;
    /** @var PaymentStatusChangedTrigger */
    protected $paymentStatusChangedTrigger;
    /** @var OrderTrackingCodeChangedTrigger */
    protected $orderTrackingCodeChangedTrigger;
    /** @var LineItemAddedTrigger */
    protected $lineItemAddedTrigger;
    /** @var LineItemChangedTrigger */
    protected $lineItemChangedTrigger;
    /** @var LineItemDeletedTrigger */
    protected $lineItemDeletedTrigger;
    /** @var PaymentInsights */
    protected $paymentInsights;
    /** @var DataProvider */
    protected $dataProvider;
    /** @var ConfigReader */
    protected $configReader;
    /** @var string */
    protected $controllersDir;
    /** @var string */
    protected $templateDir;
    /** @var ShopwareVersionHelper */
    protected $swVersionHelper;

    /**
     * @param string $controllersDir
     * @param string $templateDir
     */
    public function __construct(
        AddressChangedTrigger $addressChangedTrigger,
        OrderDeletedTrigger $orderDeletedTrigger,
        PaymentStatusChangedTrigger $paymentStatusChangedTrigger,
        OrderTrackingCodeChangedTrigger $orderTrackingCodeChangedTrigger,
        LineItemAddedTrigger $lineItemAddedTrigger,
        LineItemChangedTrigger $lineItemChangedTrigger,
        LineItemDeletedTrigger $lineItemDeletedTrigger,
        PaymentInsights $paymentInsights,
        DataProvider $dataProvider,
        ConfigReader $configReader,
        $controllersDir,
        $templateDir,
        ShopwareVersionHelper $swVersionHelper
    ) {
        $this->addressChangedTrigger           = $addressChangedTrigger;
        $this->orderDeletedTrigger             = $orderDeletedTrigger;
        $this->paymentStatusChangedTrigger     = $paymentStatusChangedTrigger;
        $this->orderTrackingCodeChangedTrigger = $orderTrackingCodeChangedTrigger;
        $this->lineItemAddedTrigger            = $lineItemAddedTrigger;
        $this->lineItemChangedTrigger          = $lineItemChangedTrigger;
        $this->lineItemDeletedTrigger          = $lineItemDeletedTrigger;
        $this->paymentInsights                 = $paymentInsights;
        $this->dataProvider                    = $dataProvider;
        $this->configReader                    = $configReader;
        $this->controllersDir                  = $controllersDir;
        $this->templateDir                     = $templateDir;
        $this->swVersionHelper                 = $swVersionHelper;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'Shopware_Controllers_Backend_Order::saveAction::replace' => [
                'onOrderChange',
            ],
            'Shopware_Controllers_Backend_Order::savePositionAction::replace' => [
                'onLineItemChange',
            ],
            'Shopware_Controllers_Backend_Order::deletePositionAction::replace' => [
                'onDeleteLineItem',
            ],
            'Shopware_Controllers_Backend_Order::deleteAction::replace' => [
                'cancelKlarnaOrderOrFail',
            ],
            'Enlight_Controller_Action_PostDispatch_Backend_Order' => [
                'loadExtJsKlarnaTab',
            ],
        ];
    }

    /**
     * Checks if changing Klarna Order Address is possible
     */
    public function onOrderChange(Enlight_Hook_HookArgs $args): void
    {
        /** @var Enlight_Controller_Action $controller */
        $controller = $args->getSubject();
        $args->setProcessed(true);
        $orderId       = $controller->Request()->getParam('id');
        $paymentId     = (int) $controller->Request()->getParam('paymentId');
        $transactionId = $controller->Request()->getParam('transactionId');
        $params        = $controller->Request()->getParams();

        if (!$this->paymentInsights->isKlarnaOrder($orderId)) {
            $args->setReturn($args->getSubject()->executeParent(
                $args->getMethod(),
                $args->getArgs()
            ));

            return;
        }

        // region PaymentStatusChange

        if (empty($transactionId)) {
            $args->setReturn($args->getSubject()->executeParent(
                $args->getMethod(),
                $args->getArgs()
            ));

            return;
        }

        $changePaymentResponse = $this->paymentStatusChangedTrigger->execute($orderId, $paymentId);

        if ($changePaymentResponse->isError()) {
            $controller->View()->assign([
                'success' => false,
                'message' => $changePaymentResponse->getError()->errorMessages,
            ]);

            return;
        }

        // endregion

        // region AddressChange

        $updateCustomerAddressesResponse = $this->addressChangedTrigger->execute(
            $orderId,
            $params['shipping'][0],
            $params['billing'][0]
        );

        if ($updateCustomerAddressesResponse->isError()) {
            $controller->View()->assign([
                'success' => false,
                'message' => $updateCustomerAddressesResponse->getError()->errorMessages,
            ]);

            return;
        }

        // endregion

        // region OrderTrackingCodeChange

        $updateTrackingCodeResponse = $this->orderTrackingCodeChangedTrigger->execute(
            $orderId,
            $params['trackingCode']
        );

        if ($updateTrackingCodeResponse->isError()) {
            $controller->View()->assign([
                'success' => false,
                'message' => $updateTrackingCodeResponse->getError()->errorMessages,
            ]);

            return;
        }

        // endregion

        $args->setReturn($args->getSubject()->executeParent(
            $args->getMethod(),
            $args->getArgs()
        ));
    }

    /**
     * Synchronizes the line item changes with Klarna.
     */
    public function onLineItemChange(Enlight_Hook_HookArgs $args): void
    {
        /** @var Enlight_Controller_Action $controller */
        $controller = $args->getSubject();
        $request    = $controller->Request();
        $args->setProcessed(true);
        $position = $controller->Request()->getParams();
        $orderId  = $request->getParam('orderId');

        $shopVersion = $this->swVersionHelper->getVersion();

        /*
         * Shopware checks from SW 5.5.0 on for the request parameter 'changed'. The parameter is missing
         * which leads to an error preventing of editing non klarna orders. So we have to add it.
         */
        if (version_compare($shopVersion, '5.5.0', '>=')) {
            $changed = $this->paymentInsights->getOrderChanged($orderId);
            $request->setParam('changed', $changed);
        }

        if (!$this->paymentInsights->isKlarnaOrder($orderId)) {
            $args->setReturn($args->getSubject()->executeParent(
                $args->getMethod(),
                $args->getArgs()
            ));

            return;
        }

        $this->registerShopForOrder($orderId);

        // If id is empty => a new line item was added
        if (empty($request->getParam('id'))) {
            $response = $this->lineItemAddedTrigger->execute(
                $orderId,
                $request->getParam('articleNumber'),
                $request->getParam('quantity'),
                $request->getParam('price'),
                $request->getParam('taxId'),
                $request->getParam('mode'),
                $request->getParam('articleName')
            );
        } else {
            $orderDetails = $this->dataProvider->getOrderDetails($orderId);

            /*
             * We got to check if just the order status has changed, so we can return here and don't call update
             */
            foreach ($orderDetails as $key => $orderDetail) {
                if ($position['id'] == $orderDetail['id'] && $position['statusId'] != $orderDetail['status']) {
                    $args->setReturn($args->getSubject()->executeParent(
                        $args->getMethod(),
                        $args->getArgs()
                    ));

                    return;
                }
            }

            $response = $this->lineItemChangedTrigger->execute($orderId, $position);
        }

        if ($response->isError()) {
            $controller->View()->assign([
                'success' => false,
                'message' => $response->getError()->errorMessages,
            ]);

            return;
        }

        $args->setReturn($args->getSubject()->executeParent(
            $args->getMethod(),
            $args->getArgs()
        ));
    }

    /**
     * Synchronizes the line item changes with Klarna.
     */
    public function onDeleteLineItem(Enlight_Hook_HookArgs $args): void
    {
        /** @var Enlight_Controller_Action $controller */
        $controller = $args->getSubject();
        $request    = $controller->Request();
        $args->setProcessed(true);
        $positions = $controller->Request()->getParam('positions', [['id' => $controller->Request()->getParam('id')]]);
        $orderId   = $controller->Request()->getParam('orderID');

        $shopVersion = $this->swVersionHelper->getVersion();
        /*
         * Shopware checks from SW 5.5.0 on for the request parameter 'changed'. The parameter is missing
         * which leads to an error preventing of editing non klarna orders. So we have to add it.
         */
        if (version_compare($shopVersion, '5.5.0', '>=')) {
            $changed = $this->paymentInsights->getOrderChanged($orderId);
            $request->setParam('changed', $changed);
        }

        if (!$this->paymentInsights->isKlarnaOrder($orderId)) {
            $args->setReturn($args->getSubject()->executeParent(
                $args->getMethod(),
                $args->getArgs()
            ));

            return;
        }

        $this->registerShopForOrder($orderId);

        $response = $this->lineItemDeletedTrigger->execute($orderId, $positions);

        if ($response->isError()) {
            $controller->View()->assign([
                'success' => false,
                'message' => $response->getError()->errorMessages,
            ]);

            return;
        }

        $args->setReturn($args->getSubject()->executeParent(
            $args->getMethod(),
            $args->getArgs()
        ));
    }

    /**
     * Cancels the order in Klarna.
     */
    public function cancelKlarnaOrderOrFail(Enlight_Hook_HookArgs $args): void
    {
        /** @var Enlight_Controller_Action $controller */
        $controller = $args->getSubject();
        $args->setProcessed(true);
        $orderId       = $controller->Request()->getParam('id');
        $transactionId = $controller->Request()->getParam('transactionId');

        if (!$this->paymentInsights->isKlarnaOrder($orderId)) {
            $args->setReturn($args->getSubject()->executeParent(
                $args->getMethod(),
                $args->getArgs()
            ));

            return;
        }

        if (empty($transactionId)) {
            $args->setReturn($args->getSubject()->executeParent(
                $args->getMethod(),
                $args->getArgs()
            ));

            return;
        }

        $cancelResponse = $this->orderDeletedTrigger->execute($orderId);

        if ($cancelResponse->isError()) {
            $controller->View()->assign([
                'success' => false,
                'message' => $cancelResponse->getError()->errorMessages,
            ]);

            return;
        }

        $args->setReturn($args->getSubject()->executeParent(
            $args->getMethod(),
            $args->getArgs()
        ));
    }

    /**
     * Load the ExtJs Tab for Klarna section
     */
    public function loadExtJsKlarnaTab(Enlight_Controller_ActionEventArgs $args): void
    {
        $view    = $args->getSubject()->View();
        $request = $args->getRequest();

        $view->addTemplateDir($this->templateDir);

        if ($request->getActionName() === 'index') {
            $view->extendsTemplate('backend/ExtJs/app.js');
        }

        if ($request->getActionName() === 'load') {
            $view->extendsTemplate('backend/ExtJs/view/detail/overview.js');
            $view->extendsTemplate('backend/ExtJs/view/detail/window.js');

            if ($this->pickwareEnabled()) {
                $view->extendsTemplate('backend/ExtJs/controller/detail-pickware.js');
            } else {
                $view->extendsTemplate('backend/ExtJs/controller/detail.js');
            }
        }
    }

    /**
     * @param int $orderId
     *
     * @throws NoOrderFoundException
     */
    protected function registerShopForOrder($orderId): void
    {
        $swOrder = $this->dataProvider->getSwOrder($orderId);

        if ($swOrder === null) {
            throw new NoOrderFoundException("Order {$orderId} can not be found");
        }

        /**
         * Registers the shop in which the order was placed.
         * This is needed to assemble the correct product and product image url.
         */
        $swOrder->getShop()->registerResources();
    }

    /**
     * If a PickWare is enabled, true will be returned.
     */
    protected function pickwareEnabled(): bool
    {
        $pickwareEnabled = (int) $this->configReader->get('pickware_enabled', 0);

        return $pickwareEnabled === 1;
    }
}
