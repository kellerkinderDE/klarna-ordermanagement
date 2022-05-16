<?php

declare(strict_types=1);

namespace BestitKlarnaOrderManagement\Components\Storage;

use BestitKlarnaOrderManagement\Components\Model\TransactionLog;
use Doctrine\DBAL\Connection;
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
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
class DataProvider
{
    /** @var EntityManagerInterface */
    protected $em;
    /** @var Connection */
    protected $connection;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em         = $em;
        $this->connection = $em->getConnection();
    }

    /**
     * @param int $id
     */
    public function getSwOrder($id): ?Order
    {
        return $this->em->find(Order::class, $id);
    }

    /**
     * @param int $shopwareOrderId
     */
    public function getKlarnaOrderId($shopwareOrderId): ?string
    {
        $klarnaOrderId = $this->connection->createQueryBuilder()
            ->select('transactionID')
            ->from('s_order')
            ->where('id = :orderId')
            ->setParameter('orderId', $shopwareOrderId)
            ->execute()
            ->fetchColumn();

        return $klarnaOrderId ?: null;
    }

    /**
     * @param int $klarnaOrderId
     */
    public function getShopwareOrderId($klarnaOrderId): ?string
    {
        $shopwareOrderId = $this->connection->createQueryBuilder()
            ->select('id')
            ->from('s_order')
            ->where('transactionID = :transactionID')
            ->setParameter('transactionID', $klarnaOrderId)
            ->execute()
            ->fetchColumn();

        return $shopwareOrderId ?: null;
    }

    /**
     * @param int $orderId
     */
    public function getOrderDetails($orderId): array
    {
        return $this->connection->createQueryBuilder()
            ->select('soda.*, sod.*, sad.id as variantId')
            ->from('s_order_details', 'sod')
            ->leftJoin('sod', 's_articles_details', 'sad', 'sod.articleordernumber = sad.ordernumber')
            ->leftJoin('sod', 's_order_details_attributes', 'soda', 'sod.id = soda.detailID')
            ->where('sod.orderID = :orderId')
            ->setParameter('orderId', $orderId)
            ->execute()
            ->fetchAll();
    }

    /**
     * @param int $countryId
     */
    public function getCountry($countryId): Country
    {
        return $this->em->find(Country::class, $countryId);
    }

    /**
     * @param int $stateId
     */
    public function getState($stateId): State
    {
        return $this->em->find(State::class, $stateId);
    }

    /**
     * @param string $klarnaOrderId
     */
    public function getTrackingInfo($klarnaOrderId): array
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
     */
    public function getArticleDetail($articleNumber): ArticleDetail
    {
        return $this->em->getRepository(ArticleDetail::class)->findOneBy([
            'number' => $articleNumber,
        ]);
    }

    /**
     * @param int $taxId
     */
    public function getTax($taxId): Tax
    {
        return $this->em->find(Tax::class, $taxId);
    }

    /**
     * @param int $statusId
     *
     * @return object|Status
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
    public function getLogs($klarnaOrderId): array
    {
        return $this->em->createQueryBuilder()
            ->select('t')
            ->from(TransactionLog::class, 't')
            ->where('t.klarnaOrderId = :orderId')
            ->orderBy('t.createdAt', 'DESC')
            ->setParameter('orderId', $klarnaOrderId)
            ->getQuery()
            ->getResult();
    }
}
