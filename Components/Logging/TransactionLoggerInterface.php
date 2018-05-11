<?php

namespace BestitKlarnaOrderManagement\Components\Logging;

use BestitKlarnaOrderManagement\Components\Api\Request;
use BestitKlarnaOrderManagement\Components\Api\Response;
use BestitKlarnaOrderManagement\Components\Model\TransactionLog;

/**
 * Logger for various Klarna transactions.
 *
 * @package BestitKlarnaOrderManagement\Components\Logging
 *
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
interface TransactionLoggerInterface
{
    /**
     * @param Request  $request
     * @param Response $response
     *
     * @return TransactionLog
     */
    public function updateOrder(Request $request, Response $response);

    /**
     * @param Request  $request
     * @param Response $response
     *
     * @return TransactionLog
     */
    public function cancelOrder(Request $request, Response $response);

    /**
     * @param Request  $request
     * @param Response $response
     *
     * @return TransactionLog
     */
    public function extendAuthTime(Request $request, Response $response);

    /**
     * @param Request  $request
     * @param Response $response
     *
     * @return TransactionLog
     */
    public function releaseRemainingAmount(Request $request, Response $response);

    /**
     * @param Request  $request
     * @param Response $response
     *
     * @return TransactionLog
     */
    public function createCapture(Request $request, Response $response);

    /**
     * @param Request  $request
     * @param Response $response
     *
     * @return TransactionLog
     */
    public function createRefund(Request $request, Response $response);
}
