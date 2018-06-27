<?php

namespace BestitKlarnaOrderManagement\Components\Installer;

use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Plugin\Context\UninstallContext;
use Shopware\Components\Plugin\Context\UpdateContext;
use Shopware\Components\Snippet\DatabaseHandler;

/**
 * Installs several needed data, such as snippets.
 *
 * @package BestitKlarnaOrderManagement\Components\Installer
 *
 * @author  Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
class Installer implements InstallerInterface
{
    /** @var DatabaseHandler */
    protected $snippetHandler;
    /** @var string */
    protected $snippetDir;

    /**
     * @param DatabaseHandler $snippetHandler
     */
    public function __construct(DatabaseHandler $snippetHandler)
    {
        $this->snippetHandler = $snippetHandler;
        $this->snippetDir = __DIR__ . '/../../Resources/snippets/';
    }

    /**
     * @param Plugin         $plugin
     * @param InstallContext $installContext
     *
     * @return void
     */
    public function install(Plugin $plugin, InstallContext $installContext)
    {
        $this->snippetHandler->loadToDatabase($this->snippetDir);
    }

    /**
     * @param Plugin        $plugin
     * @param UpdateContext $updateContext
     *
     * @return void
     */
    public function update(Plugin $plugin, UpdateContext $updateContext)
    {
        $this->snippetHandler->loadToDatabase($this->snippetDir);
    }

    /**
     * @param Plugin           $plugin
     * @param UninstallContext $uninstallContext
     *
     * @return void
     */
    public function uninstall(Plugin $plugin, UninstallContext $uninstallContext)
    {
        if ($uninstallContext->keepUserData()) {
            return;
        }

        $this->snippetHandler->removeFromDatabase($this->snippetDir);
    }
}
