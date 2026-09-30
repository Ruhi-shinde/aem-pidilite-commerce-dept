<?php
/**
 * Copyright © Embitel, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Embitel\CategoryManager\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\UrlInterface;

/**
 * CategorySplitList GraphQL resolver class to fetch categories and their products
 */
class CategorySplitList implements ResolverInterface
{
    /**
     * Initialize dependencies for the CategorySplitList resolver.
     *
     * @param CategoryFactory $categoryFactory
     * @param CollectionFactory $categoryCollectionFactory
     * @param ProductCollectionFactory $productCollectionFactory
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        protected CategoryFactory $categoryFactory,
        protected CollectionFactory $categoryCollectionFactory,
        protected ProductCollectionFactory $productCollectionFactory,
        protected StoreManagerInterface $storeManager
    ) {
    }

    /**
     * Resolve the GraphQL query to fetch categories and their products, split into standard and special categories.
     *
     * @param Field $field
     * @param mixed $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return array
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        ?array $value = null,
        ?array $args = null
    ) {
        $storeId = (int)$context->getExtensionAttributes()->getStore()->getId();
        $parentId = (int)($args['filters']['parent_id']['eq'] ?? 2);

        $allCategories = $this->getCategoriesTree($parentId, $storeId);

        $standardCategories = $this->filterStandardCategories($allCategories);
        $specialCategories = $this->filterSpecialCategories($allCategories);

        return [
            'total_count' => count($allCategories),
            'items' => [
                'standard' => array_values($standardCategories),
                'special' => array_values($specialCategories)
            ]
        ];
    }

    /**
     * Filter the categories to include only standard categories and their children.
     *
     * @param array $categories
     * @return array
     */
    protected function filterStandardCategories(array $categories): array
    {
        $filtered = [];

        foreach ($categories as $category) {
            if (!empty($category['children'])) {
                $category['children'] = $this->filterStandardCategories($category['children']);
            }

            $isStandard = isset($category['is_standard']) && (int)$category['is_standard'] === 1;
            $hasStandardChildren = !empty($category['children']);

            if ($isStandard || $hasStandardChildren) {
                $filtered[] = $category;
            }
        }

        return $filtered;
    }

    /**
     * Filter out standard categories while retaining special descendants.
     *
     * @param array $categories
     * @return array
     */
    protected function filterSpecialCategories(array $categories): array
    {
        $filtered = [];

        foreach ($categories as $category) {
            $children = !empty($category['children'])
                ? $this->filterSpecialCategories($category['children'])
                : [];
            $isStandard = isset($category['is_standard']) && (int)$category['is_standard'] === 1;

            if (!$isStandard) {
                $category['children'] = $children;
                $filtered[] = $category;
                continue;
            }

            foreach ($children as $child) {
                $filtered[] = $child;
            }
        }

        return $filtered;
    }

    /**
     * Recursively fetch categories and their products starting from a given parent category ID.
     *
     * @param int $parentId
     * @param int $storeId
     * @return array
     */
    protected function getCategoriesTree(int $parentId, int $storeId): array
    {
        $parentCategory = $this->categoryFactory->create()->load($parentId);
        $collection = $this->categoryCollectionFactory->create();
        $collection->addAttributeToSelect([
            'name',
            'url_key',
            'description',
            'category_type',
            'recommendations',
            'include_in_menu',
            'updated_at',
            'level',
            'is_standard',
            'image',
            'category_banner'
        ]);
        $collection->setStoreId($storeId);
        $collection->addAttributeToFilter('is_active', 1);
        $collection->addFieldToFilter('path', ['like' => $parentCategory->getPath() . '/%']);
        $collection->setOrder('position', 'ASC');

        $childrenByParent = [];
        foreach ($collection as $category) {
            $childrenByParent[(int)$category->getParentId()][] = $this->getCategoryData($category, $storeId);
        }

        return $this->buildCategoryTree($childrenByParent, $parentId);
    }

    /**
     * Get the data for a single category, including its products and relevant attributes.
     *
     * @param \Magento\Catalog\Model\Category $category
     * @param int $storeId
     * @return array
     */
    private function getCategoryData($category, int $storeId): array
    {
        $productCollection = $this->productCollectionFactory->create();
        $productCollection->setStoreId($storeId);
        $productCollection->addCategoryFilter($category);
        $productCollection->addAttributeToFilter('status', Status::STATUS_ENABLED);
        $productCollection->addAttributeToFilter(
            'visibility',
            ['in' => Visibility::VISIBILITY_BOTH]
        );
        $productCollection->addAttributeToSelect(['name', 'sku', 'price', 'small_image', 'url_key']);

        $productItems = [];
        foreach ($productCollection as $product) {
            $smallImage = $product->getSmallImage();
            $imageUrl = null;
            if ($smallImage && $smallImage !== 'no_selection') {
                $imageUrl = $this->storeManager->getStore($storeId)->getBaseUrl(
                    UrlInterface::URL_TYPE_MEDIA
                ) . 'catalog/product/' . ltrim($smallImage, '/');
                $product->setData('small_image', $smallImage);
                $product->setData('image', $smallImage);
                $product->setData('thumbnail', $smallImage);
            }

            $productItems[] = [
                'id' => (int)$product->getId(),
                'name' => $product->getName(),
                'sku' => $product->getSku(),
                'type_id' => $product->getTypeId(),
                'model' => $product,
                'price' => (float)$product->getPrice(),
                'url_key' => $product->getUrlKey(),
                'image' => $imageUrl
            ];
        }

        $productCount = (int)$productCollection->getSize();

        return [
            'id' => (int)$category->getId(),
            'name' => $category->getName(),
            'slug' => $category->getUrlKey(),
            'description' => $category->getDescription(),
            'category_type' => $category->getData('category_type'),
            'recommendations' => $category->getData('recommendations'),
            'include_in_menu' => (int)$category->getIncludeInMenu(),
            'updated_at' => $category->getUpdatedAt(),
            'level' => (int)$category->getLevel(),
            'is_standard' => $category->getData('is_standard') !== null
                ? (int)$category->getData('is_standard')
                : null,
            'product_count' => $productCount,
            'image' => $category->getImageUrl() ?: '',
            'category_banner' => $this->getCategoryBannerUrl($category, $storeId),
            'products' => [
                'total_count' => $productCount,
                'items' => $productItems
            ],
            'children' => []
        ];
    }

    /**
     * Build the store media URL for the category banner attribute.
     *
     * @param \Magento\Catalog\Model\Category $category
     * @param int $storeId
     * @return string
     */
    private function getCategoryBannerUrl($category, int $storeId): string
    {
        $banner = (string) $category->getData('category_banner');
        if ($banner === '') {
            return '';
        }

        return $this->storeManager->getStore($storeId)->getBaseUrl(UrlInterface::URL_TYPE_MEDIA)
            . 'catalog/category/' . ltrim($banner, '/');
    }

    /**
     * Build a hierarchical tree of categories from a flat list of categories grouped by their parent IDs.
     *
     * @param array $childrenByParent
     * @param int $parentId
     * @return array
     */
    private function buildCategoryTree(array &$childrenByParent, int $parentId): array
    {
        $categories = [];

        foreach ($childrenByParent[$parentId] ?? [] as $category) {
            $category['children'] = $this->buildCategoryTree($childrenByParent, $category['id']);
            $categories[] = $category;
        }

        return $categories;
    }
}
