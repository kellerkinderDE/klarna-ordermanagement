<?php

namespace BestitKlarnaOrderManagement\Components\Api;

/**
 * Representation of any request that is fired to Klarna.
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
    /** @var null|string */
    protected $baseUrl;

    public static function createFromPayload(array $payload): Request
    {
        $static = new static();

        $static->setPayload($payload);

        return $static;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @param string $key
     */
    public function getHeader($key): ?string
    {
        return $this->headers[$key] ?? null;
    }

    /**
     * @param string $key
     * @param string $value
     */
    public function addHeader($key, $value): Request
    {
        $this->headers[$key] = $value;

        return $this;
    }

    public function setHeaders(array $headers): Request
    {
        $this->headers = $headers;

        return $this;
    }

    public function clearHeaders(): Request
    {
        $this->headers = [];

        return $this;
    }

    /**
     * @return null|array|string
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * @param array|string $payload
     */
    public function setPayload($payload): Request
    {
        $this->payload = $payload;

        return $this;
    }

    public function getQueryParameters(): array
    {
        return $this->queryParameters;
    }

    /**
     * @param string $key
     *
     * @return null|mixed
     */
    public function getQueryParameter($key)
    {
        return $this->queryParameters[$key] ?? null;
    }

    /**
     * @param string $key
     */
    public function addQueryParameter($key, $value): Request
    {
        $this->queryParameters[$key] = $value;

        return $this;
    }

    public function setQueryParameters(array $extraData): Request
    {
        $this->queryParameters = $extraData;

        return $this;
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl ?: '';
    }

    /**
     * @param string $baseUrl
     */
    public function setBaseUrl($baseUrl): Request
    {
        $this->baseUrl = $baseUrl;

        return $this;
    }
}
