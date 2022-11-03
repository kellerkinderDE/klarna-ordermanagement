<?php

namespace BestitKlarnaOrderManagement\Components;

use Doctrine\DBAL\Connection;
use PDO;

/**
 * Collection of methods that aggregate data relating to payment means.
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
     * @param string $klarnaPluginName
     */
    public function __construct(Connection $connection, $klarnaPluginName)
    {
        $this->connection       = $connection;
        $this->klarnaPluginName = $klarnaPluginName;
    }

    /**
     * @param int $paymentId
     */
    public function isKlarnaPaymentMethodId($paymentId): bool
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
     */
    public function isKlarnaOrder($orderId): bool
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
     * @param int $orderId
     */
    public function getOrderChanged($orderId): ?string
    {
        return $this->connection->createQueryBuilder()
            ->select('o.changed')
            ->from('s_order', 'o')
            ->where('o.id = :orderId')
            ->setParameter('orderId', $orderId)
            ->execute()
            ->fetchColumn();
    }

    /**
     * Get payment using the ip
     *
     * @param int $paymentId
     */
    public function getPaymentById($paymentId): array
    {
        return $this->connection->createQueryBuilder()
            ->select('payment.*')
            ->from('s_core_paymentmeans', 'payment')
            ->where('payment.id = :paymentId')
            ->setParameter('paymentId', $paymentId)
            ->execute()
            ->fetch();
    }

    /**
     * @param int $paymentId
     */
    public function getPluginNameById($paymentId): string
    {
        return $this->connection->createQueryBuilder()
            ->select('plugin.name')
            ->from('s_core_paymentmeans', 'payment')
            ->join('payment', 's_core_plugins', 'plugin', 'payment.pluginID = plugin.id')
            ->where('payment.id = :paymentId')
            ->setParameter('paymentId', $paymentId)
            ->execute()
            ->fetchColumn();
    }

    public function getSupportedExternalCheckoutIds(): array
    {
        return $this->connection->createQueryBuilder()
            ->select('payment.id')
            ->from('s_core_paymentmeans', 'payment')
            ->join('payment', 's_core_plugins', 'plugin', 'payment.pluginID = plugin.id')
            ->andWhere('plugin.name IN (:pluginNames)')
            ->setParameter(':pluginNames', Constants::SUPPORTED_EXTERNAL_CHECKOUT, Connection::PARAM_STR_ARRAY)
            ->execute()
            ->fetchAll(PDO::FETCH_COLUMN);
    }
}
