<?php
namespace Embitel\CategoryManager\Setup\Patch\Data;

use Magento\Catalog\Model\Category;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * Data patch to add 'category_type' and 'recommendations' attributes to categories.
 */
class AddCategoryTypeAndRecommendationsAttributes implements DataPatchInterface
{
    /**
     * Injected dependencies for the data patch.
     *
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        private ModuleDataSetupInterface $moduleDataSetup,
        private EavSetupFactory $eavSetupFactory
    ) {
    }

    /**
     * Apply the data patch to add attributes to categories.
     *
     * @return $this
     */
    public function apply()
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        // 1. Add Category Type Attribute (e.g., application, segment)
        $eavSetup->addAttribute(
            Category::ENTITY,
            'category_type',
            [
                'type' => 'varchar',
                'input' => 'text',
                'label' => 'Category Type',
                'required' => false,
                'visible' => true,
                'global' => 1,
                'user_defined' => true,
                'default' => '',
                'group' => 'General Information',
                'visible_on_front' => false,
                'sort_order' => 120,
                'used_in_product_listing' => false,
            ]
        );

        // 2. Add Recommendations Attribute (for JSON data structure)
        $eavSetup->addAttribute(
            Category::ENTITY,
            'recommendations',
            [
                'type' => 'text',
                'input' => 'textarea',
                'label' => 'Recommendations (JSON)',
                'required' => false,
                'visible' => true,
                'global' => 1,
                'user_defined' => true,
                'default' => '',
                'group' => 'General Information',
                'visible_on_front' => false,
                'sort_order' => 130,
                'used_in_product_listing' => false,
            ]
        );

        return $this;
    }

    /**
     * Get dependencies
     *
     * @return array
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * Get aliases
     *
     * @return array
     */
    public function getAliases()
    {
        return [];
    }
}
