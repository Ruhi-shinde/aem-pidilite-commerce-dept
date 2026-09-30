<?php

declare(strict_types=1);

namespace Embitel\CustomAttributesGraphQl\Setup\Patch\Data;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Model\Entity\Attribute\Source\Table;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class AddSolutionTypeAttribute implements DataPatchInterface
{
    private ModuleDataSetupInterface $moduleDataSetup;

    private CategorySetupFactory $categorySetupFactory;

    private CollectionFactory $attributeSetCollectionFactory;

    private AdapterInterface $connection;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        CategorySetupFactory $categorySetupFactory,
        CollectionFactory $attributeSetCollectionFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->categorySetupFactory = $categorySetupFactory;
        $this->attributeSetCollectionFactory = $attributeSetCollectionFactory;
        $this->connection = $moduleDataSetup->getConnection();
    }

    public function apply(): void
    {
        $this->connection->startSetup();

        try {
            $categorySetup = $this->categorySetupFactory->create([
                'setup' => $this->moduleDataSetup
            ]);

            $attributeCode = 'solution_type';

            /*
             * ==========================================================
             * 1. CREATE ATTRIBUTE
             * ==========================================================
             */

            $attributeId = (int) $categorySetup->getAttributeId(
                Product::ENTITY,
                $attributeCode
            );

            if (!$attributeId) {
                $categorySetup->addAttribute(
                    Product::ENTITY,
                    $attributeCode,
                    [
                        'type' => 'int',
                        'label' => 'Solution Type',
                        'input' => 'select',
                        'source' => Table::class,
                        'required' => false,
                        'user_defined' => true,
                        'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                        'visible' => true,
                        'visible_on_front' => true,
                        'searchable' => false,
                        'filterable' => false,
                        'filterable_in_search' => false,
                        'comparable' => false,
                        'used_in_product_listing' => false,
                        'used_for_sort_by' => false,
                        'unique' => false,
                        /*
                         * We will set the actual option ID below.
                         */
                        'default' => null,

                        /*
                         * Dropdown options.
                         */
                        'option' => [
                            'values' => [
                                'Standard',
                                'Premium'
                            ]
                        ]
                    ]
                );

                $attributeId = (int) $categorySetup->getAttributeId(
                    Product::ENTITY,
                    $attributeCode
                );
            }

            /*
             * ==========================================================
             * 2. SET "STANDARD" AS DEFAULT
             * ==========================================================
             */

            $standardOptionId = $this->getOptionId(
                $attributeId,
                'Standard'
            );

            if ($standardOptionId !== null) {
                $categorySetup->updateAttribute(
                    Product::ENTITY,
                    $attributeId,
                    'default_value',
                    $standardOptionId
                );
            }

            /*
             * ==========================================================
             * 3. GET SKU ATTRIBUTE ID
             * ==========================================================
             */

            $skuAttributeId = (int) $categorySetup->getAttributeId(
                Product::ENTITY,
                'sku'
            );

            /*
             * ==========================================================
             * 4. GET ALL EXISTING PRODUCT ATTRIBUTE SETS
             * ==========================================================
             */

            $attributeSetCollection =
                $this->attributeSetCollectionFactory->create();

            $attributeSetCollection->setEntityTypeFilter(
                Product::ENTITY
            );

            /*
             * ==========================================================
             * 5. ASSIGN TO EVERY ATTRIBUTE SET
             * ==========================================================
             */

            foreach ($attributeSetCollection as $attributeSet) {
                $attributeSetId = (int) $attributeSet->getAttributeSetId();

                /*
                 * Check whether Solution Type is already assigned.
                 */
                $alreadyAssigned = $this->connection->fetchOne(
                    $this->connection->select()
                        ->from(
                            $this->moduleDataSetup->getTable(
                                'eav_entity_attribute'
                            ),
                            ['entity_attribute_id']
                        )
                        ->where(
                            'attribute_set_id = ?',
                            $attributeSetId
                        )
                        ->where(
                            'attribute_id = ?',
                            $attributeId
                        )
                        ->limit(1)
                );

                if ($alreadyAssigned) {
                    continue;
                }

                /*
                 * ------------------------------------------------------
                 * Find SKU's group and sort order.
                 * ------------------------------------------------------
                 */
                $skuData = $this->connection->fetchRow(
                    $this->connection->select()
                        ->from(
                            $this->moduleDataSetup->getTable(
                                'eav_entity_attribute'
                            ),
                            [
                                'attribute_group_id',
                                'sort_order'
                            ]
                        )
                        ->where(
                            'attribute_set_id = ?',
                            $attributeSetId
                        )
                        ->where(
                            'attribute_id = ?',
                            $skuAttributeId
                        )
                        ->limit(1)
                );

                if ($skuData) {
                    /*
                     * SKU exists in this attribute set.
                     *
                     * Put Solution Type immediately after SKU.
                     */
                    $attributeGroupId =
                        (int) $skuData['attribute_group_id'];

                    $skuSortOrder =
                        (int) $skuData['sort_order'];

                    $solutionTypeSortOrder =
                        $skuSortOrder + 1;

                    /*
                     * Shift attributes after SKU by one position.
                     */
                    $this->connection->update(
                        $this->moduleDataSetup->getTable(
                            'eav_entity_attribute'
                        ),
                        [
                            'sort_order' => new \Zend_Db_Expr(
                                'sort_order + 1'
                            )
                        ],
                        [
                            'attribute_set_id = ?' => $attributeSetId,
                            'attribute_group_id = ?' => $attributeGroupId,
                            'sort_order > ?' => $skuSortOrder
                        ]
                    );
                } else {
                    /*
                     * --------------------------------------------------
                     * SKU does not exist in this attribute set.
                     *
                     * Still assign Solution Type.
                     *
                     * Use the first attribute group.
                     * --------------------------------------------------
                     */
                    $attributeGroupId =
                        $this->getFirstAttributeGroupId(
                            $attributeSetId
                        );

                    if (!$attributeGroupId) {
                        continue;
                    }

                    /*
                     * Put it at the end of the group.
                     */
                    $solutionTypeSortOrder =
                        $this->getNextSortOrder(
                            $attributeSetId,
                            $attributeGroupId
                        );
                }

                /*
                 * ------------------------------------------------------
                 * Assign attribute to the attribute set/group.
                 * ------------------------------------------------------
                 */
                $categorySetup->addAttributeToGroup(
                    Product::ENTITY,
                    $attributeSetId,
                    $attributeGroupId,
                    $attributeCode,
                    $solutionTypeSortOrder
                );
            }
        } finally {
            $this->connection->endSetup();
        }
    }

    /**
     * Get option ID by option label.
     */
    private function getOptionId(
        int $attributeId,
        string $label
    ): ?int {
        $select = $this->connection->select()
            ->from(
                [
                    'option' => $this->moduleDataSetup->getTable(
                        'eav_attribute_option'
                    )
                ],
                ['option_id']
            )
            ->join(
                [
                    'value' => $this->moduleDataSetup->getTable(
                        'eav_attribute_option_value'
                    )
                ],
                'option.option_id = value.option_id',
                []
            )
            ->where(
                'option.attribute_id = ?',
                $attributeId
            )
            ->where(
                'value.store_id = ?',
                0
            )
            ->where(
                'value.value = ?',
                $label
            )
            ->limit(1);

        $optionId = $this->connection->fetchOne($select);

        return $optionId !== false
            ? (int) $optionId
            : null;
    }

    /**
     * Get first attribute group from attribute set.
     */
    private function getFirstAttributeGroupId(
        int $attributeSetId
    ): ?int {
        $select = $this->connection->select()
            ->from(
                $this->moduleDataSetup->getTable(
                    'eav_attribute_group'
                ),
                ['attribute_group_id']
            )
            ->where(
                'attribute_set_id = ?',
                $attributeSetId
            )
            ->order(
                'sort_order ASC'
            )
            ->limit(1);

        $groupId = $this->connection->fetchOne($select);

        return $groupId !== false
            ? (int) $groupId
            : null;
    }

    /**
     * Get next sort order for an attribute group.
     */
    private function getNextSortOrder(
        int $attributeSetId,
        int $attributeGroupId
    ): int {
        $select = $this->connection->select()
            ->from(
                $this->moduleDataSetup->getTable(
                    'eav_entity_attribute'
                ),
                [
                    'max_sort_order' => new \Zend_Db_Expr(
                        'MAX(sort_order)'
                    )
                ]
            )
            ->where(
                'attribute_set_id = ?',
                $attributeSetId
            )
            ->where(
                'attribute_group_id = ?',
                $attributeGroupId
            );

        $maxSortOrder = $this->connection->fetchOne($select);

        return ((int) $maxSortOrder) + 1;
    }

    public static function getDependencies(): array
    {
        return [];
    }

    public function getAliases(): array
    {
        return [];
    }
}