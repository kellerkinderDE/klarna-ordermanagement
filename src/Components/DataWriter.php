<?php

namespace BestitKlarnaOrderManagement\Components;

use Doctrine\DBAL\Connection;

/**
 *  Changes several data that is related to an order.
 *
 * @author Senan Sharhan <senan.sharhan@bestit-online.de>
 */
class DataWriter
{
    /** @var Connection */
    protected $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
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

        return $this->connection->update('s_order', ['cleared' => $statusId], ['transactionID' => $transactionId]);
    }
}
