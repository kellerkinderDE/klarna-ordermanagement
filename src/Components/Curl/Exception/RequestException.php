<?php

declare(strict_types=1);

namespace BestitKlarnaOrderManagement\Components\Curl\Exception;

use BestitKlarnaOrderManagement\Components\Curl\Response;
use Throwable;

class RequestException extends \Exception
{
    /** @var Response */
    private $response;

    public function __construct(Response $response, $message = "", $code = 0, Throwable $previous = null)
    {
        $this->response = $response;

        parent::__construct($message, $code, $previous);
    }

    public function getResponse()
    {
        return $this->response;
    }
}
