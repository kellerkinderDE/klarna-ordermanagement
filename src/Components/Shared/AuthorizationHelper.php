<?php

namespace BestitKlarnaOrderManagement\Components\Shared;

use BestitKlarnaOrderManagement\Components\Api\Request;
use BestitKlarnaOrderManagement\Components\ConfigReader;
use BestitKlarnaOrderManagement\Components\Storage\DataProvider;

/**
 * Builds the authorization header.
 *
 * Needed since we weren't able to change the configreader in the hook of BestitOrderManagement.
 * The BestitKlarnaOrderManagement\Components\Api\Resource classes still had the wrong configreader.
 * It seems they get build to early.
 *
 * @package BestitKlarnaOrderManagement\Components\Shared
 *
 * @author Ralf Nitzer <ralf.nitzer@bestit-online.de>
 */
class AuthorizationHelper
{
    /** @var ConfigReader */
    protected $configReader;
    /** @var DataProvider */
    protected $dataProvider;

    /**
     * @param ConfigReader $configReader
     * @param DataProvider $dataProvider
     */
    public function __construct(ConfigReader $configReader, DataProvider $dataProvider)
    {
        $this->configReader = $configReader;
        $this->dataProvider = $dataProvider;
    }

    /**
     * Needed for setting the right authorization and using sub shop credetials where necessary
     * @param Request $request
     *
     * @return string
     */
    public function setAuthHeader(Request $request)
    {
        $orderId = $request->getQueryParameter('order_id');

        if (empty($orderId)) {
            return $request;
        }

        $this->setShopForConfigReader($orderId);

        $liveMode = (bool) $this->configReader->get('live_mode');

        if ($liveMode) {
            $merchantId = $this->configReader->get('live_merchant_id');
            $merchantPassword = $this->configReader->get('live_merchant_password');
        } else {
            $merchantId = $this->configReader->get('test_merchant_id');
            $merchantPassword = $this->configReader->get('test_merchant_password');
        }

        $request->addHeader(
            'Authorization',
            'Basic ' . base64_encode("{$merchantId}:{$merchantPassword}")
        );

        return $request;
    }

    /**
     * Sets the right shop in the Config Reader
     * @param int $orderId Klarna Order Id
     *
     * @return void
     */
    protected function setShopForConfigReader($orderId)
    {
        //gets the sw orderid from klarna order id
        $swOrderId = $this->dataProvider->getShopwareOrderId($orderId);

        if (empty($swOrderId)) {
            return;
        }

        $swOrder = $this->dataProvider->getSwOrder($swOrderId);
        $shop = $swOrder->getShop();

        $this->configReader->setShop($shop);
    }
}
