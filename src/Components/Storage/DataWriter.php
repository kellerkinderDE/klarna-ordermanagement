<?php

namespace BestitKlarnaOrderManagement\Components\Storage;

use DateTime;
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

        /** @var array $order */
        $order = $this->connection->createQueryBuilder()
            ->addSelect('o.*')
            ->from('s_order', 'o')
            ->andWhere('transactionID = :transactionId')
            ->setParameter('transactionId', $transactionId)
            ->execute()
            ->fetch();

        $orderStatus = $order['status'];

        $this->connection->update('s_order', ['cleared' => $statusId], ['transactionID' => $transactionId]);
        return $this->connection->insert(
            's_order_history',
            [
                'orderID' => $order['id'],
                'previous_order_status_id' => $orderStatus,
                'order_status_id' => $orderStatus,
                'previous_payment_status_id' => $order['cleared'],
                'payment_status_id' => $statusId,
                'change_date' => (new DateTime())->format('Y-m-d H:i:s')
            ]
        );
    }
}
