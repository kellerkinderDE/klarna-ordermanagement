<?php

namespace BestitKlarnaOrderManagement\Components\Storage;

use Doctrine\DBAL\Connection;
use BestitKlarnaOrderManagement\Components\Model\TransactionLog;
use Doctrine\ORM\EntityManagerInterface;
use Shopware\Models\Article\Detail as ArticleDetail;
use Shopware\Models\Country\Country;
use Shopware\Models\Country\State;
use Shopware\Models\Order\Order;
use Shopware\Models\Order\Status;
use Shopware\Models\Tax\Tax;

/**
 * Provides several data that is related to an order.
 *
 * @package BestitKlarnaOrderManagement\Components\DataProvider
 *
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
class DataProvider
{
    /** @var EntityManagerInterface */
    protected $em;
    /** @var Connection */
    protected $connection;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->connection = $em->getConnection();
    }

    /**
     * @param int $id
     *
     * @return Order
     */
    public function getSwOrder($id)
    {
        return $this->em->find(Order::class, $id);
    }

    /**
     * @param int $shopwareOrderId
     *
     * @return string|null
     */
    public function getKlarnaOrderId($shopwareOrderId)
    {
        $klarnaOrderId = $this->connection->createQueryBuilder()
            ->select('transactionID')
            ->from('s_order')
            ->where('id = :orderId')
            ->setParameter('orderId', $shopwareOrderId)
            ->execute()
            ->fetchColumn()
        ;

        return $klarnaOrderId ?: null;
    }

    /**
     * @param int $klarnaOrderId
     *
     * @return string|null
     */
    public function getShopwareOrderId($klarnaOrderId)
    {
        $shopwareOrderId = $this->connection->createQueryBuilder()
            ->select('id')
            ->from('s_order')
            ->where('transactionID = :transactionID')
            ->setParameter('transactionID', $klarnaOrderId)
            ->execute()
            ->fetchColumn()
        ;

        return $shopwareOrderId ?: null;
    }

    /**
     * @param int $orderId
     *
     * @return array
     */
    public function getOrderDetails($orderId)
    {
        return $this->connection->createQueryBuilder()
            ->select('sod.*, sad.id as variantId')
            ->from('s_order_details', 'sod')
            ->leftJoin('sod', 's_articles_details', 'sad', 'sod.articleordernumber = sad.ordernumber')
            ->where('sod.orderID = :orderId')
            ->setParameter('orderId', $orderId)
            ->execute()
            ->fetchAll()
        ;
    }

    /**
     * @param int $countryId
     *
     * @return Country
     */
    public function getCountry($countryId)
    {
        return $this->em->find(Country::class, $countryId);
    }

    /**
     * @param int $stateId
     *
     * @return State
     */
    public function getState($stateId)
    {
        return $this->em->find(State::class, $stateId);
    }

    /**
     * @param string $klarnaOrderId
     *
     * @return array
     */
    public function getTrackingInfo($klarnaOrderId)
    {
        return $this->connection->createQueryBuilder()
            ->select('so.trackingcode as trackingCode, spd.name as dispatchName')
            ->from('s_order', 'so')
            ->join('so', 's_premium_dispatch', 'spd', 'so.dispatchID = spd.id')
            ->where('so.transactionID = :transactionId')
            ->setParameter('transactionId', $klarnaOrderId)
            ->execute()
            ->fetch();
    }

    /**
    * @param string $articleNumber
    *
    * @return ArticleDetail
    */
    public function getArticleDetail($articleNumber)
    {
        return $this->em->getRepository(ArticleDetail::class)->findOneBy([
            'number' => $articleNumber
        ]);
    }

    /**
     * @param int $taxId
     *
     * @return Tax
     */
    public function getTax($taxId)
    {
        return $this->em->find(Tax::class, $taxId);
    }

    /**
     * @param int $statusId
     *
     * @return Status|object
     */
    public function getStatusReference($statusId)
    {
        return $this->em->getReference(Status::class, $statusId);
    }

    /**
     * @param string $klarnaOrderId
     *
     * @return TransactionLog[]
     */
    public function getLogs($klarnaOrderId)
    {
        return $this->em->createQueryBuilder()
            ->select('t')
            ->from(TransactionLog::class, 't')
            ->where('t.klarnaOrderId = :orderId')
            ->orderBy('t.createdAt', 'DESC')
            ->setParameter('orderId', $klarnaOrderId)
            ->getQuery()
            ->getResult()
        ;
    }
}
