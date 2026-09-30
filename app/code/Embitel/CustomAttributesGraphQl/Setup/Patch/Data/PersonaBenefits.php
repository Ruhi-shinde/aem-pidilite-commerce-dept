<?php
declare(strict_types=1);

namespace Embitel\CustomAttributesGraphQl\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Catalog\Model\Product;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory as AttributeSetCollectionFactory;

class PersonaBenefits implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @var AttributeSetCollectionFactory
     */
    private $attributeSetCollectionFactory;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $eavSetupFactory
     * @param AttributeSetCollectionFactory $attributeSetCollectionFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory,
        AttributeSetCollectionFactory $attributeSetCollectionFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->attributeSetCollectionFactory = $attributeSetCollectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        /** @var \Magento\Eav\Setup\EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        
        $entityTypeId = $eavSetup->getEntityTypeId(Product::ENTITY);
        $groupName = 'Persona Benefits'; // Change to your desired group name
        $sortOrder = 50;                // Change to position it appropriately

        // Fetch all attribute sets belonging to the catalog product entity
        $attributeSetCollection = $this->attributeSetCollectionFactory->create()
            ->setEntityTypeFilter($entityTypeId);

        foreach ($attributeSetCollection as $attributeSet) {
            $attributeSetId = $attributeSet->getId();
            
            // This safely adds the group to the attribute set if it doesn't already exist
            $eavSetup->addAttributeGroup(
                $entityTypeId,
                $attributeSetId,
                $groupName,
                $sortOrder
            );
        }

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }
}