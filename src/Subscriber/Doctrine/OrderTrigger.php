<?php

declare(strict_types=1);

namespace BestitKlarnaOrderManagement\Subscriber\Doctrine;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Shopware\Models\Order\Detail as SwOrderDetail;
use Shopware\Models\Order\Order as SwOrderModel;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Subscriber to trigger entity changes such as billing address, shipping address
 *  and some specific fields such as order status, order Tracking number.
 *
 * @author Senan Sharhan <senan.sharhan@bestit-online.de>
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
class OrderTrigger implements EventSubscriber
{
    /** @var ContainerInterface $container */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getSubscribedEvents(): array
    {
        return [
            'preUpdate',
        ];
    }

    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $orderTrigger       = $this->container->get('bestit_klarna_order_management.components.trigger.order_status_changed');
        $orderDetailTrigger = $this->container->get(
            'bestit_klarna_order_management.components.trigger.order_detail_status_changed'
        );

        $entity = $args->getEntity();

        if ($entity instanceof SwOrderModel && $args->hasChangedField('orderStatus')) {
            $orderTrigger->executeDefinedTriggers($entity);
        }

        if ($entity instanceof SwOrderDetail && $args->hasChangedField('status') && $this->pickwareIsNotEnabled()) {
            $orderDetailTrigger->executeDefinedTriggers($entity);
        }
    }

    protected function pickwareIsNotEnabled(): bool
    {
        $configReader         = $this->container->get('bestit_klarna_order_management.components.config_reader');
        $pickwareIsNotEnabled = (int) $configReader->get('pickware_enabled', 0);

        return $pickwareIsNotEnabled === 0;
    }
}
