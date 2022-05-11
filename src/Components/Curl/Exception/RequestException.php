<?php

declare(strict_types=1);

namespace BestitKlarnaOrderManagement\Components\Curl\Exception;

use JetBrains\PhpStorm\Internal\LanguageLevelTypeAware;

class RequestException extends \Exception
{
    /** @var null|\BestitKlarnaOrderManagement\Components\Api\Response */
    private $response;

    public function __construct($response, $message = "", $code = 0, Throwable $previous = null)
    {
        $this->response = $response;

        parent::__construct(
            $message,
            $code,
            $previous
        );
    }

    public function getResponse()
    {
        return $this->response;
    }
}
