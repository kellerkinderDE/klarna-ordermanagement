<?php

declare(strict_types=1);

namespace BestitKlarnaOrderManagement\Subscriber\Plugin;

use BestitKlarnaOrderManagement\Components\ConfigReader;
use BestitKlarnaOrderManagement\Components\PaymentInsights;
use BestitKlarnaOrderManagement\Components\Pickware\CaptureOnShipped;
use BestitKlarnaOrderManagement\Components\Pickware\RefundOnCancellation;
use BestitKlarnaOrderManagement\Components\Storage\DataProvider;
use Enlight\Event\SubscriberInterface;
use Enlight_Hook_HookArgs;
use Shopware_Controllers_Backend_ViisonPickwareERPOrderCancelation;

/**
 * Subscriber for the Pickware plugin.
 *
 * @author  Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 * @author Senan Sharhan <senan.sharhan@bestit-online.de>
 */
class Pickware implements SubscriberInterface
{
    /** @var PaymentInsights */
    protected $paymentInsights;
    /** @var RefundOnCancellation */
    protected $refundOnCancellation;
    /** @var CaptureOnShipped */
    protected $captureOnShipped;
    /** @var DataProvider */
    protected $dataProvider;
    /** @var ConfigReader */
    protected $configReader;

    public function __construct(
        PaymentInsights $paymentInsights,
        RefundOnCancellation $refundOnCancellation,
        DataProvider $dataProvider,
        CaptureOnShipped $captureOnShipped,
        ConfigReader $configReader
    ) {
        $this->paymentInsights      = $paymentInsights;
        $this->refundOnCancellation = $refundOnCancellation;
        $this->dataProvider         = $dataProvider;
        $this->captureOnShipped     = $captureOnShipped;
        $this->configReader         = $configReader;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'Shopware_Controllers_Backend_ViisonPickwareERPOrderCancelation::cancelPositionsAction::before' => [
                'saveShippingCostsInMemory',
            ],
            'Shopware_Controllers_Backend_ViisonPickwareERPOrderCancelation::cancelPositionsAction::after' => [
                'refundCancelledPositions',
            ],
            'Shopware_Controllers_Backend_Order::savePositionAction::before' => [
                ['saveShippedValue', 999],
            ],
            'Shopware_Controllers_Backend_Order::savePositionAction::after' => [
                ['captureOnShippedChange', 999],
            ],
        ];
    }

    /**
     * Because Pickware just sets the shipping costs to 0 when they are cancelled we need to save the previous
     * amount somewhere, so we can refund it. The previous shipping costs isn't saved anywhere else so it'll
     * get lost. We just save it as an object property which will be used after the cancelling process is
     * done.
     */
    public function saveShippingCostsInMemory(Enlight_Hook_HookArgs $args): void
    {
        /** @var Shopware_Controllers_Backend_ViisonPickwareERPOrderCancelation $subject */
        $subject = $args->getSubject();
        $request = $subject->Request();

        $orderId = $request->getParam('orderId', 0);

        if (!$this->paymentInsights->isKlarnaOrder($orderId) || !$request->getParam('cancelShippingCosts', false)) {
            return;
        }

        $order = $this->dataProvider->getSwOrder($orderId);
        $this->refundOnCancellation->setShippingCosts($order->getInvoiceShipping(), $order->getInvoiceShippingNet());
    }

    /**
     * Trigger an automatic refund for all items that have been cancelled.
     */
    public function refundCancelledPositions(Enlight_Hook_HookArgs $args): void
    {
        /** @var Shopware_Controllers_Backend_ViisonPickwareERPOrderCancelation $subject */
        $subject = $args->getSubject();
        $request = $subject->Request();

        $success = $subject->View()->getAssign('success');
        $orderId = $request->getParam('orderId', 0);

        if ($success !== true || !$this->paymentInsights->isKlarnaOrder($orderId)) {
            return;
        }

        $this->refundOnCancellation->refundFor(
            $orderId,
            $request->getParam('canceledItems', [])
        );
    }

    /**
     * To find out if item has been really shipped and find out the difference we save the old value.
     */
    public function saveShippedValue(Enlight_Hook_HookArgs $args): void
    {
        $subject = $args->getSubject();
        $request = $subject->Request();

        $this->captureOnShipped->saveOldShippedValue($request->getParam('id'));
    }

    /**
     * Here we capture/refund items depends on the shipped value
     */
    public function captureOnShippedChange(Enlight_Hook_HookArgs $args): void
    {
        $subject = $args->getSubject();
        $request = $subject->Request();

        $pickwareCaptureAllowed = $this->configReader->get('pickware_enabled', false);

        if ($pickwareCaptureAllowed) {
            $this->captureOnShipped->captureIfShipped($request->getParam('id'));
        }
    }
}
