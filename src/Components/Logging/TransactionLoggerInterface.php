<?php

declare(strict_types=1);

namespace BestitKlarnaOrderManagement\Components\Logging;

use BestitKlarnaOrderManagement\Components\Api\Request;
use BestitKlarnaOrderManagement\Components\Api\Response;
use BestitKlarnaOrderManagement\Components\Model\TransactionLog;

/**
 * Logger for various Klarna transactions.
 *
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
interface TransactionLoggerInterface
{
    public function updateOrder(Request $request, Response $response): TransactionLog;

    public function cancelOrder(Request $request, Response $response): TransactionLog;

    public function extendAuthTime(Request $request, Response $response): TransactionLog;

    public function releaseRemainingAmount(Request $request, Response $response): TransactionLog;

    public function createCapture(Request $request, Response $response): TransactionLog;

    public function createRefund(Request $request, Response $response): TransactionLog;
}
