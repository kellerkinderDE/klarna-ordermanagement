<?php

namespace BestitKlarnaOrderManagement\Components\Curl;

use BestitKlarnaOrderManagement\Components\Api\Model\Error;
use BestitKlarnaOrderManagement\Components\Curl\Exception\RequestException;
use Psr\Log\LoggerInterface;
use BestitKlarnaOrderManagement\Components\Api\Response as ApiResponse;

class Client
{
    public const UNALLOWED_STATUS_CODES = [400, 401, 403, 404, 405];

    public const METHOD_GET = 'GET';
    public const METHOD_POST = 'POST';
    public const METHOD_PATCH = 'PATCH';

    /** @var LoggerInterface */
    private $logger;
    /** @var string */
    private $baseUri;
    /** @var array */
    private $options;

    public function __construct(string $baseUri, array $options, LoggerInterface $logger)
    {
        $this->baseUri = $baseUri;
        $this->options = $options;
        $this->logger = $logger;
    }

    public function get(string $uri, ?array $options = []): Response
    {
        return $this->request($uri, self::METHOD_GET, $options);
    }

    public function patch(string $uri, ?array $options = []): Response
    {
        return $this->request($uri, self::METHOD_PATCH, $options);
    }

    public function post(string $uri, ?array $options = []): Response
    {
        return $this->request($uri, self::METHOD_POST, $options);
    }

    /**
     * @throws RequestException
     */
    private function request(string $uri, string $method, ?array $options = []): Response
    {
        $body        = $this->getBody($options);
        $headers     = $this->getHeaders($options['headers'] ?? []);
        $handle      = $this->getCurlHandle($uri, $headers, $method, $body);
        $this->logger->debug(sprintf('ClientRequest request[body]: %s \n [headers]: %s', $body, json_encode($headers)));

        $response    = curl_exec($handle);
        $errorNumber = curl_errno($handle);
        $error       = curl_error($handle);
        $statusCode  = curl_getinfo($handle, CURLINFO_HTTP_CODE);

        curl_close($handle);

        $this->logger->debug(sprintf('ClientRequest response: %s', $response));

        if ($errorNumber !== 0 || $response === false || (in_array($statusCode, self::UNALLOWED_STATUS_CODES) && empty($response))) {
            $this->logger->error(sprintf('ClientRequest error %s (%s)', $error, $errorNumber), ['trace' => (new \RuntimeException())->getTraceAsString()]);

            throw new RequestException(null, sprintf('Request error "%s" (%s)', $error, $errorNumber));
        };

        return new Response($statusCode, $response);
    }

    private function getBody(?array $options): ?string
    {
        if (isset($options['json'])) {
            $encodedBody = json_encode($options['json']);

            if ($encodedBody === false) {
                // TODO: Errorhandling, add custom exception
                throw new \Exception('invalid json body');
            }

            return $encodedBody;
        }

        return $options['body'] ?? null;
    }

    /**
     * @param array|string[] $headerData
     *
     * @return resource
     */
    private function getCurlHandle(string $uri, array $headerData, string $method, ?string $postData)
    {
        $filterVarOptions = [];
        if (defined(FILTER_FLAG_SCHEME_REQUIRED)) {
            $filterVarOptions = FILTER_FLAG_SCHEME_REQUIRED;
        }
        if (filter_var($uri, FILTER_VALIDATE_URL, $filterVarOptions) === false) {
            $uri = sprintf('%s/%s', rtrim($this->baseUri, '/'), ltrim($uri, '/'));
        }

        $curl = curl_init($uri);

        if (!$curl) {
            // TODO: Errorhandling, add custom exception
            throw new RuntimeException('curl init failed');
        }

        curl_setopt($curl, CURLOPT_VERBOSE, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 60);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headerData);

        if (self::METHOD_POST === $method) {
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
        }

        if (self::METHOD_PATCH === $method) {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, self::METHOD_PATCH);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
        }

        return $curl;
    }

    protected function getHeaders(array $additionalHeaders): array
    {
        $headers = array_merge($this->options['headers'] ?? [], $additionalHeaders);

        $headersAsStrings = [];

        foreach ($headers as $headerName => $headerValue) {
            $headersAsStrings[] = sprintf('%s: %s', $headerName, $headerValue);
        }

        return $headersAsStrings;
    }
}