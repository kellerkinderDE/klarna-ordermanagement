<?php

namespace BestitKlarnaOrderManagement\Components\DataFormatter;

use Shopware\Bundle\StoreFrontBundle\Service\CategoryServiceInterface;
use Shopware\Bundle\StoreFrontBundle\Service\ContextServiceInterface;
use Shopware\Bundle\StoreFrontBundle\Struct\BaseProduct;
use Shopware\Bundle\StoreFrontBundle\Struct\Category;

/**
 * Builds a category breadcrumb for the given line items.
 *
 * @package BestitKlarnaOrderManagement\Components\DataFormatter
 *
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
class BreadcrumbBuilder implements BreadcrumbBuilderInterface
{
    const BREADCRUMB_SEPARATOR = ' > ';

    /** @var CategoryServiceInterface */
    protected $categoryService;
    /** @var ContextServiceInterface */
    protected $contextService;

    /**
     * @param CategoryServiceInterface $categoryService
     * @param ContextServiceInterface  $contextService
     */
    public function __construct(CategoryServiceInterface $categoryService, ContextServiceInterface $contextService)
    {
        $this->categoryService = $categoryService;
        $this->contextService = $contextService;
    }

    /**
     * Builds a breadcrumb for each line item and sets it as `$lineItems['breadcrumb']`.
     *
     * We use the category service to fetch all categories for all line items in one SQL Query instead
     * of using at least one SQL Query for each line item.
     *
     * @param array $lineItems  In the format that `Shopware_Controllers_Frontend_Checkout::getBasket()['content']`
     *                          returns it.
     *
     * @return array
     */
    public function addBreadcrumb(array $lineItems)
    {
        $baseProducts = $this->createBaseProducts($lineItems);

        $productCategories = $this->categoryService->getProductsCategories(
            $baseProducts,
            $this->contextService->getShopContext()
        );

        $productCategories = $this->transformToMainPathWithProductNumberAsKey($productCategories);

        foreach ($lineItems as $key => $lineItem) {
            $orderNumber = $lineItem['ordernumber'];

            if (!isset($productCategories[$orderNumber])) {
                continue;
            }

            $lineItems[$key]['breadcrumb'] = $productCategories[$orderNumber];
        }

        return $lineItems;
    }

    /**
     * @param Category[] $categories
     *
     * @return string
     */
    public function buildBreadcrumb(array $categories)
    {
        $categoryPath = [];

        /**
         * Shopware gives us every category as a individual item, not sorted by anything.
         * So we cannot reliably determine any breadcrumb.
         * To sort that issue out, we sort by the parentId and grab the first breadcrumb.
         */
        usort($categories, [$this, 'sortCategoryByParentId']);

        // This is the shop root category.
        $previousCategory = array_shift($categories);

        /**
         * Get the first path as "main" category since shopware does not have the concept of a "main" category.
         */
        foreach ($categories as $category) {
            if ($category->getParentId() !== $previousCategory->getId()) {
                continue;
            }

            $categoryPath[] = $category->getName();
            $previousCategory = $category;
        }

        return implode(self::BREADCRUMB_SEPARATOR, $categoryPath);
    }

    /**
     * Callback for `usort` to sort an array of categories by their parentIds.
     *
     * @param Category $category1
     * @param Category $category2
     *
     * @return int
     */
    public function sortCategoryByParentId(Category $category1, Category $category2)
    {
        if ($category1->getParentId() > $category2->getParentId()) {
            return 1;
        }

        if ($category1->getParentId() < $category2->getParentId()) {
            return -1;
        }

        return 0;
    }

    /**
     * @param array $lineItems
     *
     * @return BaseProduct[]
     */
    protected function createBaseProducts(array $lineItems)
    {
        $products = [];

        foreach ($lineItems as $lineItem) {
            $products[] = new BaseProduct(
                (int) $lineItem['articleID'],
                (int) $lineItem['articleDetailId'],
                $lineItem['ordernumber']
            );
        }

        return $products;
    }

    /**
     * Builds the main path for each product.
     *
     * @param array $productCategories
     *
     * @return array    The key is the product number whereas the value is the breadcrumb.
     */
    protected function transformToMainPathWithProductNumberAsKey(array $productCategories)
    {
        $mainPathCategories = [];

        foreach ($productCategories as $number => $categories) {
            $mainPathCategories[$number] = $this->buildBreadcrumb($categories);
        }

        return $mainPathCategories;
    }
}
