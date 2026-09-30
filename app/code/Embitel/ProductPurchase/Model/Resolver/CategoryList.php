<?php
/**
 * Copyright © Embitel, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Embitel\ProductPurchase\Model\Resolver;

use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;

class CategoryList implements ResolverInterface
{
    /**
     * CategoryList constructor
     *
     * @param CategoryCollectionFactory $categoryCollectionFactory
     * @param ProductCollectionFactory $productCollectionFactory
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        private CategoryCollectionFactory $categoryCollectionFactory,
        private ProductCollectionFactory $productCollectionFactory,
        private StoreManagerInterface $storeManager
    ) {
    }

    /**
     * @inheritdoc
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        ?array $value = null,
        ?array $args = null
    ) {
        try {
            $store = $this->storeManager->getStore();

            $parentId = isset($args['category_id'])
                ? (int)$args['category_id']
                : (int)$store->getRootCategoryId();

            $categories = $this->categoryCollectionFactory->create()
                ->addAttributeToSelect(['name', 'image', 'url_key', 'description'])
                ->addAttributeToFilter('parent_id', $parentId)
                ->addAttributeToFilter('is_active', 1)
                ->setOrder('position', 'ASC');

            if (!$categories->getSize()) {
                return [];
            }

            $categoryIds = $leafCategoryIds = [];
            foreach ($categories as $category) {
                $categoryIds[] = (int)$category->getId();
                if (!$category->hasChildren()) {
                    $leafCategoryIds[] = (int)$category->getId();
                }
            }

            /**
             * -------------------------------------------------
             * Load products for ALL leaf categories in ONE collection
             * -------------------------------------------------
             */
            $firstProducts = [];

            if (!empty($leafCategoryIds)) {
                $productCollection = $this->productCollectionFactory->create();
                $productCollection
                    ->addAttributeToSelect(['url_key'])
                    ->addCategoriesFilter(['in' => $leafCategoryIds])
                    ->addAttributeToFilter(
                        'status',
                        Status::STATUS_ENABLED
                    )
                    ->addAttributeToFilter(
                        'visibility',
                        [
                            'in' => [
                                Visibility::VISIBILITY_IN_CATALOG,
                                Visibility::VISIBILITY_BOTH
                            ]
                        ]
                    )
                    ->setOrder('entity_id', 'ASC');

                /**
                 * Build map:
                 * category_id => first product
                 */
                foreach ($productCollection as $product) {

                    foreach ($product->getCategoryIds() as $categoryId) {

                        if (!in_array($categoryId, $leafCategoryIds)) {
                            continue;
                        }

                        if (!isset($firstProducts[$categoryId])) {
                            $firstProducts[$categoryId] = $product;
                        }
                    }
                }
            }

            /**
             * -------------------------------------------------
             * Build response
             * -------------------------------------------------
             */

            $result = [];

            foreach ($categories as $category) {
                $categoryId = (int)$category->getId();

                $row = [
                    'id' => $categoryId,
                    'name' => $category->getName(),
                    'description' => $category->getDescription(),
                    'url' => $category->getUrl(),
                    'url_key' => $category->getUrlKey(),
                    'image' => $category->getImage()
                        ? $store->getBaseUrl(UrlInterface::URL_TYPE_MEDIA)
                        . 'catalog/category/'
                        . $category->getImage()
                        : null,
                    'has_children' => $category->hasChildren(),
                    'product_url' => null
                ];

                if (!$category->hasChildren() &&
                    isset($firstProducts[$categoryId])
                ) {
                    $row['product_url'] =
                        $firstProducts[$categoryId]->getProductUrl();
                    $row['product_url_key'] =
                        $firstProducts[$categoryId]->getUrlKey();
                }

                $result[] = $row;
            }

            return $result;

        } catch (\Exception $e) {
            throw new LocalizedException(
                __('Unable to fetch category list. %1', $e->getMessage())
            );
        }
    }
}
