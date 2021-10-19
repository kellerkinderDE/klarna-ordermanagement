<?php

namespace BestitKlarnaOrderManagement\Components\Shared;

use BestitKlarnaOrderManagement\Components\Api\Request;
use BestitKlarnaOrderManagement\Components\ConfigReader;
use BestitKlarnaOrderManagement\Components\Constants;
use BestitKlarnaOrderManagement\Components\Storage\DataProvider;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Builds the authorization header.
 *
 * Needed since we weren't able to change the configreader in the hook of BestitOrderManagement.
 * The BestitKlarnaOrderManagement\Components\Api\Resource classes still had the wrong configreader.
 * It seems they get build to early.
 *
 * @author Ralf Nitzer <ralf.nitzer@bestit-online.de>
 */
class AuthorizationHelper implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /** @var ConfigReader */
    protected $configReader;

    /** @var DataProvider */
    protected $dataProvider;

    public function __construct(ConfigReader $configReader, DataProvider $dataProvider)
    {
        $this->configReader = $configReader;
        $this->dataProvider = $dataProvider;
    }

    /**
     * Needed for setting the right authorization and using sub shop credetials where necessary
     *
     * @return string
     */
    public function setAuthHeader(Request $request)
    {
        $orderId = $request->getQueryParameter('order_id');

        if ($this->container->has('shop')) {
            $this->configReader->setShop($this->container->get('shop'));
        }

        if (!empty($orderId)) {
            $this->setShopForConfigReader($orderId);
        }

        $liveMode = (bool) $this->configReader->get('live_mode');

        if ($liveMode) {
            $merchantId = $this->configReader->get('live_merchant_id');
            $merchantPassword = $this->configReader->get('live_merchant_password');
            $request->setBaseUrl(Constants::LIVE_API);
        } else {
            $merchantId = $this->configReader->get('test_merchant_id');
            $merchantPassword = $this->configReader->get('test_merchant_password');
            $request->setBaseUrl(Constants::TEST_API);
        }

        $request->addHeader(
            'Authorization',
            'Basic ' . base64_encode("{$merchantId}:{$merchantPassword}")
        );

        return $request;
    }

    /**
     * Sets the right shop in the Config Reader
     *
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
        $shop = $swOrder->getLanguageSubShop();

        $this->configReader->setShop($shop);
    }
}
