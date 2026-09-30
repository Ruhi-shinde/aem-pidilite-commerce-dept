<?php
/**
 * Copyright © Embitel, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Embitel\ProductPurchase\Model\Resolver\Category;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class ProductData implements ResolverInterface
{
    /**
     * ProductData construct
     *
     * @param CollectionFactory $productCollectionFactory
     */
    public function __construct(
        private CollectionFactory $productCollectionFactory
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
        if (!$value || empty($value['model'])) {
            return null;
        }

        /** @var \Magento\Catalog\Model\Category $category */
        $category = $value['model'];

        // If category has children return null
        if ((int)$category->getChildrenCount() > 0) {
            return null;
        }


        $product = $this->productCollectionFactory->create()
            ->addAttributeToSelect(['url_key'])
            ->addCategoriesFilter(['in' => $category->getId()])
            ->addAttributeToFilter(
                'status',
                Status::STATUS_ENABLED
            )
            ->addAttributeToFilter(
                'visibility',
                [
                    'in' => [
                        Visibility::VISIBILITY_BOTH,
                        Visibility::VISIBILITY_IN_CATALOG
                    ]
                ]
            )
            ->setPageSize(1)
            ->setCurPage(1)
            ->getFirstItem();

        if (!$product->getId()) {
            return null;
        }

        switch ($field->getName()) {
            case 'product_url':
                return $product->getProductUrl();

            case 'product_url_key':
                return $product->getUrlKey();
        }

        return null;
    }
}
