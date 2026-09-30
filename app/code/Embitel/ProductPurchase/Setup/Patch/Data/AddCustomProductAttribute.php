<?php
/**
 * Copyright © Embitel, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Embitel\ProductPurchase\Setup\Patch\Data;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Embitel\ProductPurchase\Model\Product\Attribute\Pdf;

class AddCustomProductAttribute implements DataPatchInterface
{
    /**
     * Custom attribute construct
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
     * Attribute create function
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Validator\ValidateException
     */
    public function apply()
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'product_pdf',
            [
                'type' => 'varchar',
                'label' => 'Product PDF',
                'input' => 'file',
                'backend' => Pdf::class,
                'required' => false,
                'sort_order' => 50,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'group' => 'General',
                'visible' => true,
                'user_defined' => true,
                'visible_on_front' => true,
                'used_in_product_listing' => true
            ]
        );
    }

    /**
     * Get Dependencies function
     *
     * @return array|string[]
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * Get Aliases funciton
     *
     * @return array|string[]
     */
    public function getAliases()
    {
        return [];
    }
}
