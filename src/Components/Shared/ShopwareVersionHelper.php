<?php

namespace BestitKlarnaOrderManagement\Components\Shared;
use Shopware;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class to get the shopware version, the right way, since SW 5.6 removed Shopware::VERSION
 *
 * @package BestitKlarnaOrderManagement\Components\Shared
 */
class ShopwareVersionHelper
{
    /** @var ContainerInterface $container */
    protected $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Gets the current Shopware Version. Function is needed since Shopware::VERSION gets __SHOPWARE_VERION__ on composer installs
     * and Shopware::VERSION was removed in SW 5.6 doesn't
     *
     * @return string
     */
    public function getVersion()
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
