<?php

declare(strict_types=1);

namespace Embitel\CustomAttributesGraphQl\Model\Resolver;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory as AttributeCollectionFactory;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Group\CollectionFactory as AttributeGroupCollectionFactory;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class PersonaBenefits implements ResolverInterface
{
    private const ATTRIBUTE_GROUP_NAME = 'Persona Benefits';

    /**
     * @var array<int, array>
     */
    private array $attributesBySet = [];

    public function __construct(
        private readonly AttributeGroupCollectionFactory $attributeGroupCollectionFactory,
        private readonly AttributeCollectionFactory $attributeCollectionFactory
    ) {}

    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        ?array $value = null,
        ?array $args = null
    ): array {
        if (
            !isset($value['model']) ||
            !$value['model'] instanceof Product
        ) {
            return [];
        }

        /** @var Product $product */
        $product = $value['model'];

        $attributeSetId = (int)$product->getAttributeSetId();

        if (!$attributeSetId) {
            return [];
        }

        $attributes = $this->getAttributesForSet($attributeSetId);

        $result = [];

        foreach ($attributes as $attribute) {
            $attributeCode = (string)$attribute->getAttributeCode();

            if ($attributeCode === '') {
                continue;
            }

            $attributeModel = $product->getResource()->getAttribute($attributeCode);

            if (!$attributeModel || !$attributeModel->getId()) {
                continue;
            }

            /*
             * Get raw EAV value directly.
             *
             * This is important because the product model may not have
             * the attribute loaded when the attribute is not explicitly
             * requested in the GraphQL query.
             */
            $rawValue = $product->getResource()->getAttributeRawValue(
                (int)$product->getId(),
                $attributeCode,
                (int)$product->getStoreId()
            );

            if ($rawValue === null || $rawValue === '') {
                continue;
            }

            /*
             * Convert raw option IDs into frontend labels.
             */
            $displayValue = $this->getDisplayValue(
                $attributeModel,
                $rawValue
            );

            if ($displayValue === null || $displayValue === '') {
                continue;
            }

            $result[] = [
                'code'           => $attributeCode,
                'label'          => (string)$attribute->getFrontendLabel(),
                'value'          => (string)$displayValue,
                'raw_value'      => (string)$rawValue,
                'frontend_input' => (string)$attribute->getFrontendInput(),
                'sort_order'     => (int)$attribute->getSortOrder(),
            ];
        }

        return $result;
    }

    /**
     * Convert an attribute raw value into its frontend/display value.
     *
     * Handles select and multiselect attributes.
     */
    private function getDisplayValue($attribute, mixed $rawValue): ?string
    {
        if (!$attribute->usesSource()) {
            return is_scalar($rawValue)
                ? (string)$rawValue
                : null;
        }

        /*
         * Multiselect values are stored as comma-separated option IDs.
         */
        if ($attribute->getFrontendInput() === 'multiselect') {
            $optionIds = array_filter(
                array_map(
                    'trim',
                    explode(',', (string)$rawValue)
                ),
                static fn(string $value): bool => $value !== ''
            );

            $labels = [];

            foreach ($optionIds as $optionId) {
                $label = $attribute->getSource()->getOptionText($optionId);

                if ($label === null || $label === '') {
                    continue;
                }

                if (is_array($label)) {
                    $labels = array_merge($labels, $label);
                } else {
                    $labels[] = (string)$label;
                }
            }

            return $labels
                ? implode(', ', $labels)
                : null;
        }

        /*
         * Select and other source-based attributes.
         */
        $label = $attribute->getSource()->getOptionText($rawValue);

        if (is_array($label)) {
            return implode(', ', $label);
        }

        return $label !== null && $label !== ''
            ? (string)$label
            : null;
    }

    /**
     * Get attributes assigned to "Custom Attributes" group.
     *
     * @return array
     */
    private function getAttributesForSet(int $attributeSetId): array
    {
        if (isset($this->attributesBySet[$attributeSetId])) {
            return $this->attributesBySet[$attributeSetId];
        }

        $groupCollection = $this->attributeGroupCollectionFactory->create();

        $groupCollection->setAttributeSetFilter($attributeSetId);
        $groupCollection->addFieldToFilter(
            'attribute_group_name',
            self::ATTRIBUTE_GROUP_NAME
        );
        $groupCollection->setPageSize(1);

        $group = $groupCollection->getFirstItem();

        if (!$group->getId()) {
            return $this->attributesBySet[$attributeSetId] = [];
        }

        $attributeCollection = $this->attributeCollectionFactory->create();

        $attributeCollection->setAttributeSetFilter($attributeSetId);
        $attributeCollection->setAttributeGroupFilter(
            (int)$group->getId()
        );

        $attributeCollection->setOrder(
            'sort_order',
            'ASC'
        );

        $attributes = [];

        foreach ($attributeCollection as $attribute) {
            $attributes[] = $attribute;
        }

        return $this->attributesBySet[$attributeSetId] = $attributes;
    }
}
