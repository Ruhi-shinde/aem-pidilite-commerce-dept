<?php

namespace Fevicreate\Customer\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Customer\Model\Customer;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class AddCustomerAttribute implements DataPatchInterface
{
    /**
     * Custom attribute construct
     *
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param CustomerSetupFactory $customerSetupFactory
     * @param AttributeSetFactory $attributeSetFactory
     */
    public function __construct(
        private ModuleDataSetupInterface $moduleDataSetup,
        private CustomerSetupFactory $customerSetupFactory,
        private AttributeSetFactory $attributeSetFactory
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
        $this->createCustomerCustomAttribute("phone_number", "Phone Number");
        $this->createCustomerCustomAttribute("city", "City");
        $this->createCustomerCustomAttribute("state", "State");
        $this->createCustomerCustomAttribute("is_parent", "Is Parent?", "radio");
    }

    /**
     * Create attribute
     *
     * @param $code
     * @param $label
     * @param $type
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Validator\ValidateException
     */
    private function createCustomerCustomAttribute($code, $label, $type = "text")
    {
        $customerSetup = $this->customerSetupFactory->create(['setup' => $this->moduleDataSetup]);

        $customerEntity = $customerSetup->getEavConfig()->getEntityType('customer');
        $attributeSetId = $customerEntity->getDefaultAttributeSetId();

        $attributeSet = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);
        if ($type == "text") {
            $customerSetup->addAttribute(Customer::ENTITY, $code, [
                'type' => 'varchar',
                'label' => $label,
                'input' => 'text',
                'required' => false,
                'visible' => true,
                'user_defined' => true,
                'position' => 999,
                'system' => 0,
            ]);
        }

        if ($type == "radio") {
            $customerSetup->addAttribute(Customer::ENTITY, $code, [
                'type' => 'int',
                'label' => $label,
                'input' => 'boolean',
                'source'   => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                'required' => false,
                'visible' => true,
                'user_defined' => true,
                'position' => 999,
                'system' => 0,
            ]);
        }

        $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, $code)
            ->addData([
                'attribute_set_id' => $attributeSetId,
                'attribute_group_id' => $attributeGroupId,
                'used_in_forms' =>
                    [
                        'customer_account_edit',
                        'customer_account_create',
                        'adminhtml_customer',
                        'adminhtml_checkout'
                    ],
            ]);
        $attribute->save();
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
