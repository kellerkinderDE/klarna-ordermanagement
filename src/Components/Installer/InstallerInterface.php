<?php

namespace BestitKlarnaOrderManagement\Components\Installer;

use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Plugin\Context\UninstallContext;
use Shopware\Components\Plugin\Context\UpdateContext;

/**
 * Interface for the installer of the OrderManagement package.
 *
 * @package BestitKlarnaOrderManagement\Components\Installer
 *
 * @author  Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
interface InstallerInterface
{
    /**
     * @param Plugin         $plugin
     * @param InstallContext $installContext
     *
     * @return void
     *
     */
    public function install(Plugin $plugin, InstallContext $installContext);

    /**
     * @param Plugin        $plugin
     * @param UpdateContext $updateContext
     *
     * @return void
     */
    public function update(Plugin $plugin, UpdateContext $updateContext);

    /**
     * @param Plugin           $plugin
     * @param UninstallContext $uninstallContext
     *
     * @return void
     */
    public function uninstall(Plugin $plugin, UninstallContext $uninstallContext);
}
