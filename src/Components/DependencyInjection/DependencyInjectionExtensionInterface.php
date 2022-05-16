<?php

declare(strict_types=1);

namespace BestitKlarnaOrderManagement\Components\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Defines methods to be able to add dependencies to the Symfony DIC.
 *
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
interface DependencyInjectionExtensionInterface
{
    public function injectDependencies(ContainerBuilder $containerBuilder): void;
}
