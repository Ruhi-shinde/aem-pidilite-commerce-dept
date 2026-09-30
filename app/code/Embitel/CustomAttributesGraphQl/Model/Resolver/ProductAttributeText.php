<?php
declare(strict_types=1);

namespace Embitel\CustomAttributesGraphQl\Model\Resolver;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product as ProductResource;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class ProductAttributeText implements ResolverInterface
{
    /**
     * @var ProductResource
     */
    private ProductResource $productResource;

    /**
     * @param ProductResource $productResource
     */
    public function __construct(
        ProductResource $productResource
    ) {
        $this->productResource = $productResource;
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
        if (!isset($value['model']) || !$value['model'] instanceof Product) {
            return null;
        }

        /** @var Product $product */
        $product = $value['model'];

        $attributeCode = 'solution_type';

        /*
         * Get product ID from GraphQL product model
         */
        $productId = $product->getId();

        if (!$productId) {
            return null;
        }

        /*
         * Get attribute metadata
         */
        $attribute = $this->productResource->getAttribute($attributeCode);

        if (!$attribute || !$attribute->getId()) {
            return null;
        }

        /*
         * Explicitly load the attribute value from EAV.
         *
         * This is important because the GraphQL product model
         * may not contain all custom attributes.
         */
        $attributeValue = $this->productResource->getAttributeRawValue(
            $productId,
            $attributeCode,
            $product->getStoreId()
        );

        /*
         * If store-specific value doesn't exist, try admin/global scope.
         */
        if ($attributeValue === false || $attributeValue === null || $attributeValue === '') {
            $attributeValue = $this->productResource->getAttributeRawValue(
                $productId,
                $attributeCode,
                0
            );
        }

        if ($attributeValue === false || $attributeValue === null || $attributeValue === '') {
            return null;
        }

        /*
         * Get the frontend label from the option value.
         *
         * For example:
         * solution_type = 123
         * 123 => "Cloud Solution"
         */
        $attributeText = $attribute->getSource()->getOptionText($attributeValue);

        if ($attributeText === false || $attributeText === null) {
            return null;
        }

        /*
         * Handle multiselect attributes.
         */
        if (is_array($attributeText)) {
            return implode(', ', $attributeText);
        }

        return (string) $attributeText;
    }
}
