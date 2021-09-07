<?php

namespace BestitKlarnaOrderManagement\Components\Storage;

use BestitKlarnaPayments\Components\AttributeInstaller\AttributeInstaller;
use Doctrine\DBAL\Connection;

/**
 *  Changes several data that is related to an order.
 *
 * @package BestitKlarnaOrderManagement\Components
 *
 * @author Senan Sharhan <senan.sharhan@bestit-online.de>
 */
class DataWriter
{
    /** @var Connection */
    protected $connection;

    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param string $transactionId
     * @param int    $statusId
     *
     * @return int
     */
    public function updatePaymentStatus($transactionId, $statusId)
    {
        if (empty($transactionId)) {
            return 0;
        }
        return $this->connection->update('s_order', ['cleared' => $statusId], ['transactionID' => $transactionId]);
    }

    public function saveKlarnaCustomerToken(string $orderNumber, ?string $customerToken): void
    {
        if ($customerToken === null) {
            return;
        }

        $orderId = $this->connection->createQueryBuilder()
            ->select('id')
            ->from('s_order')
            ->where('ordernumber = :orderNumber')
            ->setParameter('orderNumber', $orderNumber)
            ->execute()
            ->fetchColumn();

        if ($orderId === null) {
            return;
        }

        $this->connection->update('s_order_attributes', [AttributeInstaller::KLARNA_CUSTOMER_TOKEN => $customerToken], ['orderID' => $orderId]);
    }
}
