<?php

declare(strict_types=1);

namespace BestitKlarnaOrderManagement\Components\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * Injects our dependencies into the DIC.
 *
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
class DependencyInjectionExtension implements DependencyInjectionExtensionInterface
{
    public function injectDependencies(ContainerBuilder $containerBuilder): void
    {
        $orderManagementDir = __DIR__ . '/../..';

        $containerBuilder->setParameter(
            'bestit_order_management.template_dir',
            "{$orderManagementDir}/Resources/views/"
        );
        $containerBuilder->setParameter(
            'bestit_order_management.controllers_dir',
            "{$orderManagementDir}/Controllers/"
        );

        $loader = new XmlFileLoader(
            $containerBuilder,
            new FileLocator()
        );

        $loader->load("{$orderManagementDir}/Resources/services.xml");
    }
}
