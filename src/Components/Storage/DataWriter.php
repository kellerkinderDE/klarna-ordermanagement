<?php

declare(strict_types=1);

namespace BestitKlarnaOrderManagement\Components\Storage;

use DateTime;
use Doctrine\DBAL\Connection;
use Psr\Log\LoggerInterface;
use RuntimeException;

/**
 *  Changes several data that is related to an order.
 *
 * @author Senan Sharhan <senan.sharhan@bestit-online.de>
 */
class DataWriter
{
    /** @var Connection */
    protected $connection;

    /** @var LoggerInterface */
    protected $logger;

    public function __construct(Connection $connection, LoggerInterface $logger)
    {
        $this->connection = $connection;
        $this->logger     = $logger;
    }

    /**
     * @param string $transactionId
     * @param int    $statusId
     */
    public function updatePaymentStatus($transactionId, $statusId): int
    {
        if (empty($transactionId)) {
            return 0;
        }

        /** @var array $order */
        $order = $this->connection->createQueryBuilder()
            ->addSelect('o.*')
            ->from('s_order', 'o')
            ->andWhere('transactionID = :transactionId')
            ->setParameter('transactionId', $transactionId)
            ->execute()
            ->fetch();

        if (
            !\is_array($order) ||
            !\array_key_exists('status', $order) ||
            !\array_key_exists('id', $order) ||
            !\array_key_exists('cleared', $order)
        ) {
            throw new RuntimeException(sprintf('No order with the transactionId %s could be found.', $transactionId));
        }

        $orderStatus = $order['status'];

        $this->connection->update('s_order', ['cleared' => $statusId], ['transactionID' => $transactionId]);

        return $this->connection->insert(
            's_order_history',
            [
                'orderID'                    => $order['id'],
                'previous_order_status_id'   => $orderStatus,
                'order_status_id'            => $orderStatus,
                'previous_payment_status_id' => $order['cleared'],
                'payment_status_id'          => $statusId,
                'change_date'                => (new DateTime())->format('Y-m-d H:i:s'),
            ]
        );
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
                'Save customer token failed. OrderId for orderNumber could not be found.',
                [
                    'orderNumber' => $orderNumber,
                ]
            );

            return;
        }

        $this->connection->update('s_order_attributes', ['klarna_customer_token' => $customerToken], ['orderID' => $orderId]);
    }
}
