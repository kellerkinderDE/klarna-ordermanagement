<?php

namespace BestitKlarnaOrderManagement\Components\Factory;

use sArticles;
use sBasket;
use Shopware\Models\Shop\DetachedShop;
use Shopware_Components_Modules;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Factory class to create several shopware modules
 *
 * Factory class to create different Shopware services which cannot be injected using DI due to how these
 * services are loaded.
 *
 * @package BestitKlarnaOrderManagement\Components\Factory
 *
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
class ShopwareModules
{
    /**
     * @param ContainerInterface $container
     *
     * @return sBasket
     */
    public static function createAdminModule(ContainerInterface $container)
    {
        /** @var Shopware_Components_Modules $swModules */
        $swModules = $container->get('modules');

        return $swModules->Admin();
    }

    /**
     * @param ContainerInterface $container
     *
     * @return sBasket
     */
    public static function createBasketModule(ContainerInterface $container)
    {
        /** @var Shopware_Components_Modules $swModules */
        $swModules = $container->get('modules');

        return $swModules->Basket();
    }

    /**
     * @param ContainerInterface $container
     *
     * @return DetachedShop
     */
    public static function createShopwareShop(ContainerInterface $container)
    {
        return $container->get('shop');
    }

    /**
     * @param ContainerInterface $container
     *
     * @return sArticles
     */
    public static function createArticlesModule(ContainerInterface $container)
    {
        /** @var Shopware_Components_Modules $swModules */
        $swModules = $container->get('modules');

        return $swModules->Articles();
    }
}
