<?php

declare(strict_types=1);

namespace BestitKlarnaOrderManagement\Components\Installer;

use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Plugin\Context\UninstallContext;
use Shopware\Components\Plugin\Context\UpdateContext;

/**
 * @author  Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
interface InstallerInterface
{
    public function install(Plugin $plugin, InstallContext $installContext): void;

    public function update(Plugin $plugin, UpdateContext $updateContext): void;

    public function uninstall(Plugin $plugin, UninstallContext $uninstallContext): void;
}
