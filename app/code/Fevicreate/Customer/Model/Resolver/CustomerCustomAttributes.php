<?php

namespace Fevicreate\Customer\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Eav\Model\Config;

class CustomerCustomAttributes implements ResolverInterface
{
    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var Config
     */
    protected $eavConfig;

    /**
     * @param CustomerRepositoryInterface $customerRepository
     * @param Config $eavConfig
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        Config $eavConfig
    ) {
        $this->customerRepository = $customerRepository;
        $this->eavConfig = $eavConfig;
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
        // Ensure the customer context exists (is logged in)
        if (!isset($value['model'])) {
            return null;
        }

        /** @var \Magento\Customer\Model\Customer $customerModel */
        $customerModel = $value['model'];

        // Re-load via repository to guarantee custom attributes/EAV data loads cleanly
        $customerData = $this->customerRepository->getById($customerModel->getId());
        $customAttributes = $customerData->getCustomAttributes();

        $output = [];
        if (!empty($customAttributes)) {
            foreach ($customAttributes as $attribute) {
                $attributeLabel = $this->eavConfig->getAttribute('customer', $attribute->getAttributeCode());
                $output[] = [
                    'attribute_code' => $attribute->getAttributeCode(),
                    'label' => $attributeLabel->getStoreLabel(),
                    'value'          => is_array($attribute->getValue())
                        ? implode(',', $attribute->getValue())
                        : $attribute->getValue()
                ];
            }
        }

        return $output;
    }
}
