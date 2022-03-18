<?php

namespace BestitKlarnaOrderManagement\Components;

use Shopware\Components\Plugin\ConfigReader as SwConfigReaderInterface;
use Shopware\Models\Shop\Shop;

/**
 * Class to retrieve plugin configuration values for the current (sub-)shop.
 *
 * @package BestitKlarnaOrderManagement\Components
 *
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
class ConfigReader
{
    /** @var SwConfigReaderInterface */
    protected $swConfigReader;
    /** @var string */
    protected $pluginName;
    /** @var Shop|null */
    protected $shop;
    /** @var array */
    protected $data = [];

    /**
     * @param SwConfigReaderInterface $swConfigReader
     * @param string                  $pluginName
     */
    public function __construct(SwConfigReaderInterface $swConfigReader, $pluginName)
    {
        $this->swConfigReader = $swConfigReader;
        $this->pluginName = $pluginName;
    }

    /**
     * @return Shop|null
     */
    public function getShop()
    {
        return $this->shop;
    }

    /**
     * @param Shop $shop
     *
     * @return ConfigReader
     */
    public function setShop(Shop $shop)
    {
        $this->shop = $shop;

        return $this;
    }

    /**
     * Retrieve plugin config value for the current sub-shop.
     *
     * @param string $name
     * @param mixed  $defaultReturn
     *
     * @return mixed
     */
    public function get($name, $defaultReturn = null)
    {
        $shopId = 0;

        if ($this->shop !== null) {
            $shopId = $this->shop->getId();
        }

        $key = "s{$shopId}";

        if (!array_key_exists($key, $this->data) || $this->data[$key] === null) {
            $this->data[$key] = $this->swConfigReader->getByPluginName($this->pluginName, $this->shop);
        }

        if (!array_key_exists($name, $this->data[$key])) {
            return $defaultReturn;
        }

        return $this->data[$key][$name];
    }
}
