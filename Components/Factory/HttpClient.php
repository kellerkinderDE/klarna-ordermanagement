<?php

namespace BestitKlarnaOrderManagement\Components\Factory;

use BestitKlarnaOrderManagement\Components\ConfigReader;
use BestitKlarnaOrderManagement\Components\Constants;
use BestitKlarnaOrderManagement\Components\Api\Middleware\Logging as LoggingMiddleware;
use GuzzleHttp\Client as GuzzleHttpClient;
use Shopware;

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
     * @param ConfigReader      $configReader
     * @param LoggingMiddleware $loggingMiddleware
     * @param string            $pluginName
     * @param string            $pluginVersion
     *
     * @return GuzzleHttpClient
     */
    public static function create(
        ConfigReader $configReader,
        LoggingMiddleware $loggingMiddleware,
        $pluginName,
        $pluginVersion
    ) {
        $liveMode = (bool) $configReader->get('live_mode');
        $shopVersion = Shopware::VERSION;

        if ($liveMode) {
            $merchantId = $configReader->get('live_merchant_id');
            $merchantPassword = $configReader->get('live_merchant_password');
        } else {
            $merchantId = $configReader->get('test_merchant_id');
            $merchantPassword = $configReader->get('test_merchant_password');
        }

        $config = [
            'base_url' => $liveMode ? Constants::LIVE_API : Constants::TEST_API,
            'defaults' => [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'User-Agent' => "Shopware {$shopVersion}/{$pluginName} {$pluginVersion}",
                ],
                'auth' => [$merchantId, $merchantPassword]
            ]
        ];

        $client = new GuzzleHttpClient($config);
        $client->getEmitter()->attach($loggingMiddleware);

        return $client;
    }
}
