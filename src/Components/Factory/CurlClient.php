<?php

namespace BestitKlarnaOrderManagement\Components\Factory;

use BestitKlarnaOrderManagement\Components\ConfigReader;
use BestitKlarnaOrderManagement\Components\Constants;
use BestitKlarnaOrderManagement\Components\Api\Middleware\Logging as LoggingMiddleware;
use BestitKlarnaOrderManagement\Components\Api\Middleware\LoggingGuzzle7 as LoggingGuzzle7Middleware;
use BestitKlarnaOrderManagement\Components\Curl\Client;
use BestitKlarnaOrderManagement\Components\Shared\PluginHelper;
use BestitKlarnaOrderManagement\Components\Shared\ShopwareVersionHelper;
use GuzzleHttp\Client as GuzzleHttpClient;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;

/**
 * Factory responsible for creating a HttpClient.
 *
 * @package BestitKlarnaOrderManagement\Components\Factory
 *
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
class CurlClient
{
    /**
     * @param ConfigReader             $configReader
     * @param LoggingMiddleware        $loggingMiddleware
     * @param LoggingGuzzle7Middleware $loggingGuzzle7Middleware
     * @param ShopwareVersionHelper    $swVersionHelper
     * @param PluginHelper             $pluginHelper
     *
     * @return Client
     */
    public static function create(
        ConfigReader $configReader,
        LoggingMiddleware $loggingMiddleware,
        LoggingGuzzle7Middleware $loggingGuzzle7Middleware,
        ShopwareVersionHelper $swVersionHelper,
        PluginHelper $pluginHelper
    ) {
        $liveMode = (bool) $configReader->get('live_mode');
        $shopVersion = $swVersionHelper->getVersion();
        $pluginName = $pluginHelper->getPluginName();
        $pluginVersion = $pluginHelper->getPluginVersion();

        $config = [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'User-Agent' => "Shopware {$shopVersion}/{$pluginName} {$pluginVersion}",
            ],
        ];

        $client = new Client(
            $liveMode ? Constants::LIVE_API : Constants::TEST_API,
            $config
        );

        return $client;
    }
}
