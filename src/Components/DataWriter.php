<?php

namespace BestitKlarnaOrderManagement\Components;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Shopware\Models\Order\Order;
use Shopware\Models\Order\Status;

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
}
