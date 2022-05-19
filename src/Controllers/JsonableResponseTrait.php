<?php

namespace BestitKlarnaOrderManagement\Controllers;

use Exception;
use InvalidArgumentException;
use JsonSerializable;

/**
 * Helper to return json responses from controllers.
 *
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
trait JsonableResponseTrait
{
    /**
     * @param array|JsonSerializable $data
     *
     * @throws InvalidArgumentException
     */
    public function jsonResponse($data): void
    {
        if (!is_array($data) && !$data instanceof JsonSerializable) {
            throw new InvalidArgumentException('The given input can not be JSON serialized.');
        }

        $this->Response()->setHeader('Content-Type', 'application/json', true);
        $this->Response()->setBody(json_encode($data));
    }

    public function jsonException(Exception $e): void
    {
        $this->jsonResponse([
            'success' => false,
            'error'   => $e->getMessage(),
        ]);
    }
}
