<?php

namespace BestitKlarnaOrderManagement\Components\Api;

/**
 * Representation of any request that is fired to Klarna.
 *
 * @package BestitKlarnaOrderManagement\Components\Api
 *
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
class Request
{
    /** @var array */
    protected $headers = [];
    /** @var array */
    protected $payload = [];
    /** @var array */
    protected $queryParameters = [];
    /** @var string|null */
    protected $baseUrl;

    /**
     * @param array $payload
     *
     * @return Request
     */
    public static function createFromPayload(array $payload)
    {
        $static = new static();

        $static->setPayload($payload);

        return $static;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param string $key
     *
     * @return string|null
     */
    public function getHeader($key)
    {
        return isset($this->headers[$key]) ? $this->headers[$key] : null;
    }

    /**
     * @param string $key
     * @param string $value
     *
     * @return Request
     */
    public function addHeader($key, $value)
    {
        $this->headers[$key] = $value;

        return $this;
    }

    /**
     * @param array $headers
     *
     * @return Request
     */
    public function setHeaders(array $headers)
    {
        $this->headers = $headers;

        return $this;
    }

    /**
     * @return Request
     */
    public function clearHeaders()
    {
        $this->headers = [];

        return $this;
    }

    /**
     * @return array|string|null
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * @param array|string $payload
     *
     * @return Request
     */
    public function setPayload($payload)
    {
        $this->payload = $payload;

        return $this;
    }

    /**
     * @return array
     */
    public function getQueryParameters()
    {
        return $this->queryParameters;
    }

    /**
     * @param string $key
     *
     * @return mixed|null
     */
    public function getQueryParameter($key)
    {
        return isset($this->queryParameters[$key]) ? $this->queryParameters[$key] : null;
    }

    /**
     * @param string $key
     * @param mixed $value
     *
     * @return Request
     */
    public function addQueryParameter($key, $value)
    {
        $this->queryParameters[$key] = $value;

        return $this;
    }

    /**
     * @param array $extraData
     *
     * @return Request
     */
    public function setQueryParameters(array $extraData)
    {
        $this->queryParameters = $extraData;

        return $this;
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->baseUrl ?: '';
    }

    /**
     * @param string $baseUrl
     *
     * @return Request
     */
    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = $baseUrl;

        return $this;
    }
}
