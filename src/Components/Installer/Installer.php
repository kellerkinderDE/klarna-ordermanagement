<?php

declare(strict_types=1);

namespace BestitKlarnaOrderManagement\Components\Installer;

use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Plugin\Context\UninstallContext;
use Shopware\Components\Plugin\Context\UpdateContext;
use Shopware\Components\Snippet\DatabaseHandler;

/**
 * Installs several needed data, such as snippets.
 *
 * @author  Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
class Installer implements InstallerInterface
{
    /** @var DatabaseHandler */
    protected $snippetHandler;
    /** @var string */
    protected $snippetDir;

    public function __construct(DatabaseHandler $snippetHandler)
    {
        $this->snippetHandler = $snippetHandler;
        $this->snippetDir     = __DIR__ . '/../../Resources/snippets/';
    }

    public function install(Plugin $plugin, InstallContext $installContext): void
    {
        $this->snippetHandler->loadToDatabase($this->snippetDir);
    }

    public function update(Plugin $plugin, UpdateContext $updateContext): void
    {
        $this->snippetHandler->loadToDatabase($this->snippetDir);
    }

    public function uninstall(Plugin $plugin, UninstallContext $uninstallContext): void
    {
        if ($uninstallContext->keepUserData()) {
            return;
        }

        $this->snippetHandler->removeFromDatabase($this->snippetDir);
    }
}
