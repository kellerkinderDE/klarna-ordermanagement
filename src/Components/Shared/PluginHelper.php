<?php

declare(strict_types=1);

namespace BestitKlarnaOrderManagement\Components\Shared;

use Shopware\Bundle\PluginInstallerBundle\Service\InstallerService;

class PluginHelper
{
    /** @var InstallerService */
    private $swInstallerService;

    /** @var string */
    private $pluginName;

    public function __construct(InstallerService $swInstallerService, string $pluginName)
    {
        $this->swInstallerService = $swInstallerService;
        $this->pluginName         = $pluginName;
    }

    public function getPluginName(): string
    {
        return $this->pluginName;
    }

    public function getPluginVersion(): string
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
