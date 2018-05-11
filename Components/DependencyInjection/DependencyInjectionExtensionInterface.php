<?php

namespace BestitKlarnaOrderManagement\Components\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Defines methods to be able to add dependencies to the Symfony DIC.
 *
 * @package BestitKlarnaOrderManagement\Components\DependencyInjection
 *
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
interface DependencyInjectionExtensionInterface
{
    /**
     * @param ContainerBuilder $containerBuilder
     *
     * @return void
     */
    public function injectDependencies(ContainerBuilder $containerBuilder);
}
