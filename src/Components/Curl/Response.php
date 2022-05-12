<?php

namespace BestitKlarnaOrderManagement\Components\Curl;

use BestitKlarnaOrderManagement\Components\Api\Model\Error;

class Response
{
    /** @var int */
    private $statusCode;
    /** @var null|string */
    private $body;
    /** @var null|Error  */
    private $error;

    public function __construct(int $statusCode, ?string $body, ?Error $error = null) {
        $this->statusCode = $statusCode;
        $this->body = $body;
        $this->error = $error;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function getError(): ?Error
    {
        return $this->error;
    }
}
