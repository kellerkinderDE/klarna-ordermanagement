<?php

namespace BestitKlarnaOrderManagement\Components\DataFormatter;

use Shopware\Components\Routing\Router;

/**
 * Builds the complete product URLs for line items (by default it is a "shopware.php" URL).
 *
 * @package BestitKlarnaOrderManagement\Components\DataFormatter
 *
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
class ProductUrlBuilder implements ProductUrlBuilderInterface
{
    /** @var Router */
    protected $router;

    /**
     * @param Router $router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * @param array $lineItems  In the format that `Shopware_Controllers_Frontend_Checkout::getBasket()['content']`
     *                          returns it.
     *
     * @return array
     */
    public function buildProductUrls(array $lineItems)
    {
        $linkDetails = $this->router->generateList(array_column($lineItems, 'linkDetails'));

        foreach ($lineItems as $key => $lineItem) {
            $articleId = (int) $lineItem['articleID'];

            if ($articleId === 0) {
                $lineItems[$key]['linkDetails'] = null;
                continue;
            }

            $lineItems[$key]['linkDetails'] = $linkDetails[$key];
        }

        return $lineItems;
    }
}
