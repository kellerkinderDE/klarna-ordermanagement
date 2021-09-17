<?php

namespace BestitKlarnaOrderManagement\Components\Storage;

use Doctrine\DBAL\Connection;
use Psr\Log\LoggerInterface;

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

    /** @var LoggerInterface */
    protected $logger;

    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection, LoggerInterface $logger)
    {
        $this->connection = $connection;
        $this->logger     = $logger;
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

        if ($orderId === false) {
            $this->logger->error(
                'OrderId for order could not be found, to save customer token',
                [
                    'orderNumber' => $orderNumber,
                ]
            );
            return;
        }

        $this->connection->update('s_order_attributes', ['klarna_customer_token' => $customerToken], ['orderID' => $orderId]);
    }
}
