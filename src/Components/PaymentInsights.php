<?php

namespace BestitKlarnaOrderManagement\Components;

use Doctrine\DBAL\Connection;

/**
 * Collection of methods that aggregate data relating to payment means.
 *
 * @package BestitKlarnaOrderManagement\Components
 *
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
class PaymentInsights
{
    /** @var Connection */
    protected $connection;
    /** @var string */
    protected $klarnaPluginName;

    /**
     * @param Connection $connection
     * @param string     $klarnaPluginName
     */
    public function __construct(Connection $connection, $klarnaPluginName)
    {
        $this->connection = $connection;
        $this->klarnaPluginName = $klarnaPluginName;
    }

    /**
     * @param int $paymentId
     *
     * @return bool
     */
    public function isKlarnaPaymentMethodId($paymentId)
    {
        $count = $this->connection->createQueryBuilder()
            ->select('payment.id')
            ->from('s_core_paymentmeans', 'payment')
            ->join('payment', 's_core_plugins', 'plugin', 'payment.pluginID = plugin.id')
            ->where('payment.id = :paymentId')
            ->andWhere('plugin.name = :pluginName')
            ->setParameter('pluginName', $this->klarnaPluginName)
            ->setParameter('paymentId', $paymentId)
            ->execute()
            ->rowCount();

        return $count > 0;
    }

    /**
     * @param int $orderId
     *
     * @return bool
     */
    public function isKlarnaOrder($orderId)
    {
        $count = $this->connection->createQueryBuilder()
            ->select('payment.id')
            ->from('s_core_paymentmeans', 'payment')
            ->join('payment', 's_core_plugins', 'plugin', 'payment.pluginID = plugin.id')
            ->join('payment', 's_order', 'swOrder', 'payment.id = swOrder.paymentID')
            ->andWhere('plugin.name = :pluginName')
            ->andWhere('swOrder.id = :orderId')
            ->setParameter('pluginName', $this->klarnaPluginName)
            ->setParameter('orderId', $orderId)
            ->execute()
            ->rowCount();

        return $count > 0;
    }

    /**
     * Get payment using the ip
     *
     * @param int $paymentId
     *
     * @return array
     */
    public function getPaymentById($paymentId)
    {
        return $this->connection->createQueryBuilder()
            ->select('payment.*')
            ->from('s_core_paymentmeans', 'payment')
            ->where('payment.id = :paymentId')
            ->setParameter('paymentId', $paymentId)
            ->execute()
            ->fetch();
    }
}
