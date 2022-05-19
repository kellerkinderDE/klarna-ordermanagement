<?php

namespace BestitKlarnaOrderManagement\Components\Factory;

use sAdmin;
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
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
class ShopwareModules
{
    public static function createAdminModule(ContainerInterface $container): sAdmin
    {
        /** @var Shopware_Components_Modules $swModules */
        $swModules = $container->get('modules');

        return $swModules->Admin();
    }

    public static function createBasketModule(ContainerInterface $container): sBasket
    {
        /** @var Shopware_Components_Modules $swModules */
        $swModules = $container->get('modules');

        return $swModules->Basket();
    }

    public static function createShopwareShop(ContainerInterface $container): DetachedShop
    {
        return $container->get('shop');
    }

    public static function createArticlesModule(ContainerInterface $container): sArticles
    {
        /** @var Shopware_Components_Modules $swModules */
        $swModules = $container->get('modules');

        return $swModules->Articles();
    }
}
