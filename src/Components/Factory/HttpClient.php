<?php

namespace BestitKlarnaOrderManagement\Components\Factory;

use BestitKlarnaOrderManagement\Components\ConfigReader;
use BestitKlarnaOrderManagement\Components\Constants;
use BestitKlarnaOrderManagement\Components\Api\Middleware\Logging as LoggingMiddleware;
use BestitKlarnaOrderManagement\Components\Api\Middleware\LoggingGuzzle7 as LoggingGuzzle7Middleware;
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
class HttpClient
{
    /**
     * @param ConfigReader             $configReader
     * @param LoggingMiddleware        $loggingMiddleware
     * @param LoggingGuzzle7Middleware $loggingGuzzle7Middleware
     * @param ShopwareVersionHelper    $swVersionHelper
     * @param PluginHelper             $pluginHelper
     *
     * @return GuzzleHttpClient
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

        if ($liveMode) {
            $merchantId = $configReader->get('live_merchant_id');
            $merchantPassword = $configReader->get('live_merchant_password');
        } else {
            $merchantId = $configReader->get('test_merchant_id');
            $merchantPassword = $configReader->get('test_merchant_password');
        }

        /*
         * Check Version to make compatibility to older Guzzle clients which
         * had an events system. In Guzzle version 6.0.0 the event system had been removed.
         */
        if ((defined('GuzzleHttp\Client::VERSION') && version_compare(GuzzleHttpClient::VERSION, '6.0.0', '>='))
            || (defined('GuzzleHttp\Client::MAJOR_VERSION') && version_compare(GuzzleHttpClient::MAJOR_VERSION, '7', '>='))
        ) {
            $handler = HandlerStack::create();
            $handler->push($loggingGuzzle7Middleware);

            $config = [
                'base_uri' => $liveMode ? Constants::LIVE_API : Constants::TEST_API,
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'User-Agent' => "Shopware {$shopVersion}/{$pluginName} {$pluginVersion}",
                ],
                'auth' => [$merchantId, $merchantPassword],
                'handler' => $handler
            ];

            $client = new GuzzleHttpClient($config);
        } else {
            $config = [
                'base_url' => $liveMode ? Constants::LIVE_API : Constants::TEST_API,
                'defaults' => [
                    'headers' => [
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                        'User-Agent' => "Shopware {$shopVersion}/{$pluginName} {$pluginVersion}",
                    ],
                    'auth' => [$merchantId, $merchantPassword],

                ]
            ];

            $client = new GuzzleHttpClient($config);
            $client->getEmitter()->attach($loggingMiddleware);
        }

        return $client;
    }
}
