<?php

if (!interface_exists('GuzzleHttp\Event\SubscriberInterface')) {
    namespace GuzzleHttp\Event;
    interface SubscriberInterface {}
}

namespace BestitKlarnaOrderManagement\Components\Api\Middleware;

use BestitKlarnaOrderManagement\Components\ConfigReader;
use GuzzleHttp\Event\CompleteEvent;
use GuzzleHttp\Event\RequestEvents;
use GuzzleHttp\Event\SubscriberInterface;
use Monolog\Logger;

/**
 * Middleware to logs the Klarna request and response.
 * Depends on what the ShopOwner selected, everything will be logged, Errors or nothing.
 *
 * @package BestitKlarnaOrderManagement\Components\Api\Middleware
 *
 * @author Senan Sharhan <senan.sharhan@bestit-online.de>
 */
class Logging implements SubscriberInterface
{
    const LOG_LEVEL_NONE = 1;
    const LOG_LEVEL_ERRORS = 2;
    const LOG_LEVEL_ALL = 3;

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
     * @return array
     */
    public function getEvents()
    {
        return [
            'complete' => ['onComplete', RequestEvents::REDIRECT_RESPONSE],
        ];
    }

    /**
     * @param CompleteEvent $event
     *
     * @return void
     */
    public function onComplete(CompleteEvent $event)
    {
        $request = $event->getRequest();
        $response = $event->getResponse();

        // Remove authorization header for securty reason
        if($request->getHeader('Authorization') !== null) {
            $request->setHeader('Authorization', 'REMOVED');
        }

        $logLevel = $this->configReader->get('log_level', self::LOG_LEVEL_ERRORS);

        if ($logLevel === self::LOG_LEVEL_NONE) {
            return;
        }

        if ($logLevel === self::LOG_LEVEL_ERRORS) {
            if ($this->isErrorStatusCode($response->getStatusCode())) {
                return;
            }

            $this->logger->error($request);
            $this->logger->error($response);

            return;
        }

        $logLevel = $this->isErrorStatusCode($response->getStatusCode()) ? Logger::DEBUG : Logger::ERROR;

        $this->logger->log($logLevel, $request);
        $this->logger->log($logLevel, $response);
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
}
