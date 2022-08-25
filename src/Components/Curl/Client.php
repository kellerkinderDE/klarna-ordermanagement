<?php

namespace BestitKlarnaOrderManagement\Components\Curl;

use BestitKlarnaOrderManagement\Components\Api\Model\Error;
use BestitKlarnaOrderManagement\Components\Curl\Exception\CurlInitException;
use BestitKlarnaOrderManagement\Components\Curl\Exception\JsonException;
use BestitKlarnaOrderManagement\Components\Curl\Exception\KlarnaCurlClientException;
use BestitKlarnaOrderManagement\Components\Curl\Exception\RequestException;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Throwable;

class Client
{
    public const METHOD_GET   = 'GET';
    public const METHOD_POST  = 'POST';
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
        $this->logger  = $logger;
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

    protected function getHeaders(array $additionalHeaders): array
    {
        $headers = array_merge($this->options['headers'] ?? [], $additionalHeaders);

        $headersAsStrings = [];

        foreach ($headers as $headerName => $headerValue) {
            $headersAsStrings[] = sprintf('%s: %s', $headerName, $headerValue);
        }

        return $headersAsStrings;
    }

    /**
     * @throws KlarnaCurlClientException
     * @throws RequestException
     * @throws JsonException
     * @throws CurlInitException
     */
    private function request(string $uri, string $method, ?array $options = []): Response
    {
        $body    = $this->getBody($options);
        $headers = $this->getHeaders($options['headers'] ?? []);
        $handle  = $this->getCurlHandle($uri, $headers, $method, $body);
        $this->logger->debug(sprintf('ClientRequest request[body]: %s \n [headers]: %s', $body, json_encode($headers)));

        $response    = curl_exec($handle);
        $errorNumber = curl_errno($handle);
        $error       = curl_error($handle);
        $statusCode  = curl_getinfo($handle, CURLINFO_HTTP_CODE);

        curl_close($handle);

        $this->logger->debug(sprintf('ClientRequest response: %s', $response));

        if ($errorNumber !== 0 || $response === false) {
            throw new RequestException(new Response($statusCode, $body));
        }

        if (!empty($response)) {
            $decodedJsonResponse = null;

            try {
                $decodedJsonResponse = json_decode($response, true);
            } catch (Throwable $t) {
                // silent fail -> could be no json
            }

            if ($decodedJsonResponse !== null) {
                if (!array_key_exists('error_code', $decodedJsonResponse)
                && !array_key_exists('error_messages', $decodedJsonResponse)) {
                    return new Response($statusCode, $response);
                }

                $error = new Error();

                if (array_key_exists('error_code', $decodedJsonResponse)) {
                    $error->errorCode = $decodedJsonResponse['error_code'];
                }

                if (array_key_exists('error_messages', $decodedJsonResponse)) {
                    $error->errorMessages = $decodedJsonResponse['error_messages'];
                }

                if (array_key_exists('correlation_id', $decodedJsonResponse)) {
                    $error->correlationId = $decodedJsonResponse['correlation_id'];
                }

                if ($error->errorCode !== null || $error->errorMessages !== null) {
                    $this->logger->error(sprintf('ClientRequest error %s (%s)', $error->errorCode, json_encode($error->errorMessages)), ['trace' => (new RuntimeException())->getTraceAsString()]);

                    throw new RequestException(new Response($statusCode, $body, $error));
                }
            }

            // fallback if response is not valid json
            if (!$this->isValidJson($response)) {
                $statusCode = $this->extractStatusCode($response);
                $body       = $this->extractBody($response);

                $error                = new Error();
                $error->errorCode     = $statusCode;
                $error->errorMessages = $body;

                throw new RequestException(new Response($statusCode, $body, $error));
            }
        }

        return new Response($statusCode, $response);
    }

    /**
     * @throws \BestitKlarnaOrderManagement\Components\Curl\Exception\JsonException
     */
    private function getBody(?array $options): ?string
    {
        if (isset($options['json'])) {
            $encodedBody = json_encode($options['json']);

            if ($encodedBody === false) {
                throw new JsonException(null, 'could not encode json');
            }

            return $encodedBody;
        }

        return $options['body'] ?? null;
    }

    /**
     * @param array|string[] $headerData
     *
     * @throws \BestitKlarnaOrderManagement\Components\Curl\Exception\CurlInitException
     *
     * @return resource
     */
    private function getCurlHandle(string $uri, array $headerData, string $method, ?string $postData)
    {
        $filterVarOptions = [];

        if (defined('FILTER_FLAG_SCHEME_REQUIRED')) {
            $filterVarOptions = FILTER_FLAG_SCHEME_REQUIRED;
        }

        if (filter_var($uri, FILTER_VALIDATE_URL, $filterVarOptions) === false) {
            $uri = sprintf('%s/%s', rtrim($this->baseUri, '/'), ltrim($uri, '/'));
        }

        $curl = curl_init($uri);

        if (!$curl) {
            throw new CurlInitException(null, 'curl init failed');
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

    private function isValidJson(string $jsonData): bool
    {
        json_decode($jsonData);

        return json_last_error() === JSON_ERROR_NONE;
    }

    private function extractStatusCode(string $jsonData): int
    {
        return (int) preg_match('/\d{3}/', $jsonData, $out) ? $out[0] : 401;
    }

    private function extractBody(string $jsonData): string
    {
        $match = preg_match('/<title>(.*?)<\/title>/', $jsonData, $out) ? $out[0] : 'empty response';

        return str_replace(['<title>', '</title>'], ['', ''], $match);
    }
}
