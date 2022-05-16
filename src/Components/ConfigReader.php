<?php

declare(strict_types=1);

namespace BestitKlarnaOrderManagement\Components;

use Shopware\Components\Plugin\ConfigReader as SwConfigReaderInterface;
use Shopware\Models\Shop\Shop;

/**
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
class ConfigReader
{
    /** @var SwConfigReaderInterface */
    protected $swConfigReader;
    /** @var string */
    protected $pluginName;
    /** @var null|Shop */
    protected $shop;
    /** @var array */
    protected $data = [];

    /**
     * @param string $pluginName
     */
    public function __construct(SwConfigReaderInterface $swConfigReader, $pluginName)
    {
        $this->swConfigReader = $swConfigReader;
        $this->pluginName     = $pluginName;
    }

    public function getShop(): ?Shop
    {
        return $this->shop;
    }

    public function setShop(Shop $shop): ConfigReader
    {
        $this->shop = $shop;

        return $this;
    }

    /**
     * Retrieve plugin config value for the current sub-shop.
     *
     * @param string $name
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
