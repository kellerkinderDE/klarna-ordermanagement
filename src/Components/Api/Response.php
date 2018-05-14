<?php

namespace BestitKlarnaOrderManagement\Components\Api;

use BestitKlarnaOrderManagement\Components\Api\Model\Error;

/**
 * Representation of any Klarna response.
 *
 * @package BestitKlarnaOrderManagement\Components\Api
 *
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
class Response
{
    /** @var bool */
    protected $isError = false;
    /** @var Error|null */
    protected $error;
    /** @var object|null */
    protected $responseObject;
    /** @var string */
    protected $rawResponse;

    /**
     * @param object $object
     *
     * @return Response
     */
    public static function wrapObject($object)
    {
        $responseWrapper = new static();

        $responseWrapper->setResponseObject($object);

        return $responseWrapper;
    }

    /**
     * @return Response
     */
    public static function wrapEmptySuccessResponse()
    {
        return new static();
    }

    /**
     * @param Error $error
     *
     * @return Response
     */
    public static function wrapError(Error $error)
    {
        $responseWrapper = new static();
        $responseWrapper
            ->setIsError(true)
            ->setError($error)
        ;

        return $responseWrapper;
    }

    /**
     * @return bool
     */
    public function isSuccessful()
    {
        return !$this->isError;
    }

    /**
     * @return bool
     */
    public function isError()
    {
        return $this->isError;
    }

    /**
     * @param bool $isError
     *
     * @return Response
     */
    public function setIsError($isError)
    {
        $this->isError = $isError;

        return $this;
    }

    /**
     * @return Error|null
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @param Error $error
     *
     * @return Response
     */
    public function setError(Error $error)
    {
        $this->error = $error;
        $this->isError = true;

        return $this;
    }

    /**
     * @return object
     */
    public function getResponseObject()
    {
        return $this->responseObject;
    }

    /**
     * @param object $responseObject
     *
     * @return Response
     */
    public function setResponseObject($responseObject)
    {
        $this->responseObject = $responseObject;

        return $this;
    }

    /**
     * @return string
     */
    public function getRawResponse()
    {
        return $this->rawResponse;
    }

    /**
     * @param string $rawResponse
     *
     * @return Response
     */
    public function setRawResponse($rawResponse)
    {
        $this->rawResponse = $rawResponse;

        return $this;
    }
}
