<?php

namespace BestitKlarnaOrderManagement\Controllers;

use Exception;
use InvalidArgumentException;
use JsonSerializable;

/**
 * Helper to return json responses from controllers.
 *
 * @package BestitKlarnaOrderManagement\Controllers
 *
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
trait JsonableResponseTrait
{
    /**
     * @param array|JsonSerializable $data
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public function jsonResponse($data)
    {
        if (!is_array($data) && !$data instanceof JsonSerializable) {
            throw new InvalidArgumentException('The given input can not be JSON serialized.');
        }

        $this->Response()->setHeader('Content-Type', 'application/json', true);
        $this->Response()->setBody(json_encode($data));
    }

    /**
     * @param Exception $e
     *
     * @return void
     */
    public function jsonException(Exception $e)
    {
        $this->jsonResponse([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
}
