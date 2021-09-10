<?php

namespace BestitKlarnaOrderManagement\Components\Shared;


use Shopware\Bundle\PluginInstallerBundle\Service\InstallerService;

class PluginHelper
{
    /** @var InstallerService */
    private $swInstallerService;

    /** @var string */
    private $pluginName;

    /**
     * @param InstallerService $swInstallerService
     * @param string $pluginName
     */
    public function __construct(InstallerService $swInstallerService, string $pluginName)
    {
        $this->swInstallerService = $swInstallerService;
        $this->pluginName = $pluginName;
    }

    /**
     * @return string
     */
    public function getPluginName()
    {
        return $this->pluginName;
    }

    /**
     * @return string
     */
    public function getPluginVersion()
    {
        try {
            $plugin = $this->swInstallerService->getPluginByName($this->pluginName);
            if ($plugin !== null) {
                $pluginVersion = $plugin->getVersion();
            }
        } catch (\Exception $exception) {
            $pluginVersion = 'UNKNOWN';
        }

        return $pluginVersion;
    }
}
