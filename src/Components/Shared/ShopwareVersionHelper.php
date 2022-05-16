<?php

declare(strict_types=1);

namespace BestitKlarnaOrderManagement\Components\Shared;

use Shopware;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ShopwareVersionHelper
{
    /** @var ContainerInterface $container */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Gets the current Shopware Version. Function is needed since Shopware::VERSION gets __SHOPWARE_VERION__ on composer installs
     * and Shopware::VERSION was removed in SW 5.6 doesn't
     */
    public function getVersion(): string
    {
        if ($this->container->hasParameter('shopware.release.version')) {
            // Get version from the shopware dic
            $version = $this->container->getParameter('shopware.release.version');
        } else {
            // Get the version by the old const
            $version = Shopware::VERSION;
        }

        return $version;
    }
}
