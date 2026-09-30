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

class IsStandard implements ResolverInterface
{
    /**
     * IsStandard construct
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

        return $category->getData('is_standard');
    }
}
