<?php

namespace BestitKlarnaOrderManagement\Components\Logging;

use BestitKlarnaOrderManagement\Components\Api\Request;
use BestitKlarnaOrderManagement\Components\Api\Response;
use BestitKlarnaOrderManagement\Components\Constants;
use DateTime;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Type;

/**
 * Logger for various Klarna transactions.
 *
 * @package BestitKlarnaOrderManagement\Components\Logging
 *
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
class TransactionLogger implements TransactionLoggerInterface
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
     * @param Request  $request
     * @param Response $response
     *
     * @return int
     */
    public function updateOrder(Request $request, Response $response)
    {
        $payload = $request->getPayload();

        return $this->log(
            $response,
            $request->getQueryParameter('order_id'),
            Constants::UPDATE_ORDER_ACTION,
            $payload['order_amount']
        );
    }

    /**
     * @param Request  $request
     * @param Response $response
     *
     * @return int
     */
    public function cancelOrder(Request $request, Response $response)
    {
        return $this->log(
            $response,
            $request->getQueryParameter('order_id'),
            Constants::CANCEL_ORDER_ACTION
        );
    }

    /**
     * @param Request  $request
     * @param Response $response
     *
     * @return int
     */
    public function extendAuthTime(Request $request, Response $response)
    {
        return $this->log(
            $response,
            $request->getQueryParameter('order_id'),
            Constants::EXTEND_AUTH_TIME_ACTION
        );
    }

    /**
     * @param Request  $request
     * @param Response $response
     *
     * @return int
     */
    public function releaseRemainingAmount(Request $request, Response $response)
    {
        return $this->log(
            $response,
            $request->getQueryParameter('order_id'),
            Constants::RELEASE_REMAINING_AMOUNT_ACTION
        );
    }

    /**
     * @param Request  $request
     * @param Response $response
     *
     * @return int
     */
    public function createCapture(Request $request, Response $response)
    {
        $payload = $request->getPayload();
        $orderId = $request->getQueryParameter('order_id');
        $cents = $payload['captured_amount'];

        return $this->log(
            $response,
            $orderId,
            Constants::CREATE_CAPTURE_ACTION,
            $cents
        );
    }

    /**
     * @param Request  $request
     * @param Response $response
     *
     * @return int
     */
    public function createRefund(Request $request, Response $response)
    {
        $payload = $request->getPayload();

        return $this->log(
            $response,
            $request->getQueryParameter('order_id'),
            Constants::CREATE_REFUND_ACTION,
            $payload['refunded_amount']
        );
    }

    /**
     * Logs all the required parameters in the default format.
     *
     * @param Response $response
     * @param string   $klarnaOrderId
     * @param string   $action
     * @param int|null $cents
     *
     * @return int
     */
    protected function log(Response $response, $klarnaOrderId, $action, $cents = null)
    {
        /**
         * Do *NOT* use the Doctrine ORM here. This will be used in an Doctrine preUpdateEvent
         * using a flush here will re-trigger any events and it will end up in a loop.
         */
        $insertData = [
            'created_at' => Type::getType(Type::DATETIME)->convertToDatabaseValue(
                new DateTime(),
                $this->connection->getDatabasePlatform()
            ),
            'action' => $action,
            'klarna_order_id' => $klarnaOrderId,
            'cents' => $cents,
            'is_successful' => (int) $response->isSuccessful()
        ];

        if (!$response->isSuccessful()) {
            $error = $response->getError();

            $errorMessages = Type::getType(Type::SIMPLE_ARRAY)->convertToDatabaseValue(
                $error->errorMessages,
                $this->connection->getDatabasePlatform()
            );

            $insertData['error_code'] = $error->errorCode;
            $insertData['error_messages'] = $errorMessages;
            $insertData['correlation_id'] = $error->correlationId;
        }

        return $this->connection->insert('bestit_klarna_transaction_log', $insertData);
    }
}
