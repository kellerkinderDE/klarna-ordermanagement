<?php

namespace BestitKlarnaOrderManagement\Components\Api\Middleware;

use BestitKlarnaOrderManagement\Components\ConfigReader;

use Closure;
use Exception;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise\PromiseInterface;
use InvalidArgumentException;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Monolog\Logger;


/**
 * Middleware to logs the Klarna request and response from Guzzle 7 where no event system is available.
 * Depends on what the ShopOwner selected, everything will be logged, Errors or nothing.
 *
 * @package BestitKlarnaOrderManagement\Components\Api\Middleware
 */
class LoggingGuzzle7
{
    const LOG_LEVEL_NONE = 1;
    const LOG_LEVEL_ERRORS = 2;
    const LOG_LEVEL_ALL = 3;

    const LOG_LEVEL_MAPPING = [
        '2' => 'ERROR',
        '3' => 'DEBUG'
    ];

    /** @var Logger */
    protected $logger;
    /** @var ConfigReader */
    protected $configReader;

    /**
     * @param Logger $logger
     * @param ConfigReader $configReader
     */
    public function __construct(Logger $logger, ConfigReader $configReader)
    {
        $this->logger = $logger;
        $this->configReader = $configReader;
    }

    /**
     * Called when the middleware is handled by the client.
     *
     * @return callable(RequestInterface, array): PromiseInterface
     */
    public function __invoke(callable $handler): callable
    {
        return function (RequestInterface $request, array $options) use ($handler): PromiseInterface {
            return $handler($request, $options)
                ->then(
                    $this->handleSuccess($request, $options),
                    $this->handleFailure($request, $options)
                );
        };
    }

    /**
     * Returns a function which is handled when a request was successful.
     *
     * @param RequestInterface $request
     * @param array $options
     *
     * @return callable
     */
    private function handleSuccess(RequestInterface $request, array $options): callable
    {
        return function (ResponseInterface $response) use ($request, $options) {
            // Remove authorization header for securty reason
            if($request->getHeader('Authorization') !== null) {
                $request->withHeader('Authorization', 'REMOVED');
            }

            $requestMessage = $request !== null ? $this->getMessage($request) : '';
            $responseMessage = $response !== null ? $this->getMessage($response) : '';

            $logLevel = (int) $this->configReader->get('log_level', self::LOG_LEVEL_ERRORS);

            if ($logLevel === self::LOG_LEVEL_NONE) {
                return $response;
            }

            if ($logLevel === self::LOG_LEVEL_ERRORS) {
                if ($this->isErrorStatusCode($response->getStatusCode())) {
                    return $response;
                }

                $this->logger->error($requestMessage);
                $this->logger->error($responseMessage);

                return $response;
            }

            $logLevel = $this->isErrorStatusCode($response->getStatusCode()) ? Logger::DEBUG : Logger::ERROR;

            $this->logger->log($logLevel, $requestMessage);
            $this->logger->log($logLevel, $responseMessage);

            return $response;
        };
    }

    /**
     * Returns a function which is handled when a request was rejected.
     *
     * @param RequestInterface $request
     * @param array $options
     *
     * @return Closure
     */
    private function handleFailure(RequestInterface $request, array $options)
    {
        return function (Exception $reason) use ($request, $options) {
            // Remove authorization header for securty reason
            if($request->getHeader('Authorization') !== null) {
                $request->withHeader('Authorization', 'REMOVED');
            }

            $requestMessage = $request !== null ? $this->getMessage($request) : '';
            $responseMessage = $reason->getResponse() !== null ? $this->getMessage($reason->getResponse()) : '';

            $this->logger->error($requestMessage);

            if ($reason instanceof RequestException && $reason->hasResponse() === true) {
                $this->logger->error($responseMessage);

                return \GuzzleHttp\Promise\rejection_for($reason);
            }

            $this->logger->error($reason->getCode());
            $this->logger->error($reason->getMessage());

            return \GuzzleHttp\Promise\rejection_for($reason);
        };
    }

    /**
     * When an API call fails Klarna will respond with a 4xx or 5xx status code
     *
     * @param int $responseStatusCode
     *
     * @return bool
     */
    protected function isErrorStatusCode($responseStatusCode)
    {
        return $responseStatusCode < 400;
    }

    /**
     * Get the Message of the reqest/response
     *
     * @param MessageInterface $message
     *
     * @return string
     */
    private function getMessage($message)
    {
        return $this->getStartLineAndHeaders($message)
            . "\r\n\r\n" . $message->getBody();
    }

    /**
     * Gets the start line of a message
     *
     * @param MessageInterface $message
     *
     * @return string
     * @throws InvalidArgumentException
     */
    private function getStartLine(MessageInterface $message)
    {
        if ($message instanceof RequestInterface) {
            return trim($message->getMethod() . ' '
                    . $message->getRequestTarget())
                . ' HTTP/' . $message->getProtocolVersion();
        } elseif ($message instanceof ResponseInterface) {
            return 'HTTP/' . $message->getProtocolVersion() . ' '
                . $message->getStatusCode() . ' '
                . $message->getReasonPhrase();
        } else {
            throw new InvalidArgumentException('Unknown message type');
        }
    }

    /**
     * Gets the headers of a message as a string
     *
     * @param MessageInterface $message
     *
     * @return string
     */
    private function getHeadersAsString(MessageInterface $message)
    {
        $result  = '';
        foreach ($message->getHeaders() as $name => $values) {
            $result .= "\r\n{$name}: " . implode(', ', $values);
        }

        return $result;
    }

    /**
     * Gets the start-line and headers of a message as a string
     *
     * @param MessageInterface $message
     *
     * @return string
     */
    private function getStartLineAndHeaders(MessageInterface $message)
    {
        return $this->getStartLine($message)
            . $this->getHeadersAsString($message);
    }

}
