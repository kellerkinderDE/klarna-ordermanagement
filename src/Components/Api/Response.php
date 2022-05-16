<?php

namespace BestitKlarnaOrderManagement\Components\Api;

use BestitKlarnaOrderManagement\Components\Api\Model\Error;

/**
 * Representation of any Klarna response.
 *
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
class Response
{
    /** @var bool */
    protected $isError = false;
    /** @var null|Error */
    protected $error;
    /** @var null|object */
    protected $responseObject;
    /** @var string */
    protected $rawResponse = '';
    /** @var int */
    protected $statusCode = 0;

    /**
     * @param object $object
     */
    public static function wrapObject($object): Response
    {
        $responseWrapper = new static();
        $responseWrapper
            ->setResponseObject($object);

        return $responseWrapper;
    }

    public static function wrapEmptySuccessResponse(): Response
    {
        return new static();
    }

    public static function wrapError(Error $error): Response
    {
        $responseWrapper = new static();
        $responseWrapper
            ->setIsError(true)
            ->setError($error);

        return $responseWrapper;
    }

    public function isSuccessful(): bool
    {
        return !$this->isError;
    }

    public function isError(): bool
    {
        return $this->isError;
    }

    /**
     * @param bool $isError
     */
    public function setIsError($isError): Response
    {
        $this->isError = $isError;

        return $this;
    }

    public function getError(): ?Error
    {
        return $this->error;
    }

    public function setError(Error $error): Response
    {
        $this->error   = $error;
        $this->isError = true;

        return $this;
    }

    public function getResponseObject(): object
    {
        return $this->responseObject;
    }

    /**
     * @param object $responseObject
     */
    public function setResponseObject($responseObject): Response
    {
        $this->responseObject = $responseObject;

        return $this;
    }

    public function getRawResponse(): string
    {
        return $this->rawResponse;
    }

    /**
     * @param string $rawResponse
     */
    public function setRawResponse($rawResponse): Response
    {
        $this->rawResponse = $rawResponse;

        return $this;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @param int $code
     */
    public function setStatusCode($code): Response
    {
        $this->statusCode = $code;

        return $this;
    }
}
