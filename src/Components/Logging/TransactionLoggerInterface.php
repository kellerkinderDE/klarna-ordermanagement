<?php

namespace BestitKlarnaOrderManagement\Components\Logging;

use BestitKlarnaOrderManagement\Components\Api\Request;
use BestitKlarnaOrderManagement\Components\Api\Response;

/**
 * Logger for various Klarna transactions.
 *
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
interface TransactionLoggerInterface
{
    public function updateOrder(Request $request, Response $response): int;

    public function cancelOrder(Request $request, Response $response): int;

    public function extendAuthTime(Request $request, Response $response): int;

    public function releaseRemainingAmount(Request $request, Response $response): int;

    public function createCapture(Request $request, Response $response): int;

    public function createRefund(Request $request, Response $response): int;
}
