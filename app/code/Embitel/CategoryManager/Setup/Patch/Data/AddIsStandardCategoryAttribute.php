<?php
namespace Embitel\CategoryManager\Setup\Patch\Data;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute as EavAttribute;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * Data patch to add the 'is_standard' attribute to categories.
 */
class AddIsStandardCategoryAttribute implements DataPatchInterface
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
     * Apply the data patch to add the 'is_standard' attribute to categories.
     *
     * @return $this
     */
    public function apply()
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        $eavSetup->addAttribute(
            Category::ENTITY,
            'is_standard',
            [
                'type' => 'int',
                'input' => 'boolean',
                'source' => \Magento\Eav\Model\Entity\Attribute\Source\Boolean::class,
                'label' => 'Is Standard',
                'required' => false,
                'visible' => true,
                'global' => 1,
                'user_defined' => true,
                'default' => 0,
                'group' => 'General Information',
                'visible_on_front' => false,
                'sort_order' => 100,
                'used_in_product_listing' => false,
            ]
        );

        return $this;
    }

    /**
     * Get dependencies for the data patch.
     *
     * @return array
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * Get aliases for the data patch.
     *
     * @return array
     */
    public function getAliases()
    {
        return [];
    }
}
