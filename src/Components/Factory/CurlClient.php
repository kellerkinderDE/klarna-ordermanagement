<?php

namespace BestitKlarnaOrderManagement\Components\Factory;

use BestitKlarnaOrderManagement\Components\ConfigReader;
use BestitKlarnaOrderManagement\Components\Constants;
use BestitKlarnaOrderManagement\Components\Curl\Client;
use BestitKlarnaOrderManagement\Components\Shared\PluginHelper;
use BestitKlarnaOrderManagement\Components\Shared\ShopwareVersionHelper;
use Psr\Log\LoggerInterface;

class CurlClient
{
    /**
     * @return Client
     */
    public static function create(
        ConfigReader $configReader,
        ShopwareVersionHelper $swVersionHelper,
        PluginHelper $pluginHelper,
        LoggerInterface $logger
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
            $config,
            $logger
        );

        return $client;
    }
}
