<?php

namespace BestitKlarnaOrderManagement\Components\DataFormatter;

use Shopware\Components\Routing\Router;

/**
 * Builds the complete product URLs for line items (by default it is a "shopware.php" URL).
 *
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
class ProductUrlBuilder implements ProductUrlBuilderInterface
{
    /** @var Router */
    protected $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * @param array $lineItems in the format that `Shopware_Controllers_Frontend_Checkout::getBasket()['content']`
     *                         returns it
     */
    public function addProductUrls(array $lineItems): array
    {
        /**
         * When trying to generate the URLs in the backend, they are incorrect.
         * This is due to the module being "backend" and the router building
         * upon that instead of generating the URLs based on the frontend
         * module where the articles are actually located.
         *
         * To fix that we just set the module and controller to the values
         * that we need and reset them after we're done.
         */
        $routerContext    = $this->router->getContext();
        $oldContextParams = $routerContext->getParams();

        if (!in_array($routerContext->getModuleKey(), ['frontend', 'widgets'])) {
            $routerContext->setParams([
                'module'     => 'frontend',
                'controller' => 'detail',
            ]);
        }

        $linkDetails = [];

        foreach ($lineItems as $lineItem) {
            $articleId = (int) $lineItem['articleID'];

            if ($articleId === 0) {
                continue;
            }

            $linkDetails[$articleId] = $lineItem['linkDetails'];
        }

        $linkDetails = $this->router->generateList($linkDetails);

        $routerContext->setParams($oldContextParams);

        foreach ($lineItems as $key => $lineItem) {
            $articleId = (int) $lineItem['articleID'];

            $lineItems[$key]['linkDetails'] = $linkDetails[$articleId] ?? null;
        }

        return $lineItems;
    }
}
