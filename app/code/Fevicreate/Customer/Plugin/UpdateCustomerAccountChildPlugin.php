<?php

namespace Fevicreate\Customer\Plugin;

use Magento\Framework\App\RequestInterface;
use Fevicreate\Customer\Model\ChildrenFactory;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;

class UpdateCustomerAccountChildPlugin
{
    /**
     * @var RequestInterface
     */
    private $request;

    private $childrenFactory;

    private $customerCollectionFactory;

    private $storeManager;

    /**
     * @param RequestInterface $request
     * @param ChildrenFactory $childrenFactory
     * @param CollectionFactory $customerCollectionFactory
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        RequestInterface $request,
        ChildrenFactory $childrenFactory,
        CollectionFactory $customerCollectionFactory,
        StoreManagerInterface $storeManager
    ) {
        $this->request = $request;
        $this->childrenFactory = $childrenFactory;
        $this->customerCollectionFactory = $customerCollectionFactory;
        $this->storeManager = $storeManager;
    }

    public function beforeResolve(
        $subject,
        Field $field,
        $context,
        ResolveInfo $info,
        ?array $value = null,
        ?array $args = null
    ) {
        $phone = "";
        $store = $context->getExtensionAttributes()->getStore();
        $websiteId = $store ? (int)$store->getWebsiteId() : (int)$this->storeManager->getStore()->getWebsiteId();

        if (isset($args['input']) && isset($args['input']['custom_attributes']) && is_array($args['input']['custom_attributes'])) {
            foreach ($args['input']['custom_attributes'] as $attribute) {
                if (isset($attribute['attribute_code'])
                    && $attribute['attribute_code'] == 'phone_number'
                    && isset($attribute['value'])) {
                    $phone = (string)$attribute['value'];
                    break;
                }
            }
        }

        if ($phone !== '') {
            $collection = $this->customerCollectionFactory->create();
            $collection->addAttributeToSelect('entity_id')
                ->addAttributeToFilter("phone_number", $phone)
                ->addAttributeToFilter("website_id", $websiteId);

            if ($context && method_exists($context, 'getUserId') && (int)$context->getUserId() > 0) {
                $collection->addAttributeToFilter('entity_id', ['neq' => (int)$context->getUserId()]);
            }

            if ($collection->getSize() > 0) {
                throw new GraphQlInputException(
                    __('A customer with the same phone number already exists in an associated website.')
                );
            }
        }

        return [$field, $context, $info, $value, $args];
    }

    /**
     * @param \Magento\CustomerGraphQl\Model\Resolver\UpdateCustomer $subject
     * @param $result
     * @param Field $field
     * @param $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return mixed
     * @throws \Exception
     */
    public function afterResolve(
        \Magento\CustomerGraphQl\Model\Resolver\UpdateCustomer $subject,
        $result,
        Field $field,
        $context,
        ResolveInfo $info,
        ?array $value = null,
        ?array $args = null
    ) {
        if (isset($result['customer'])) {
            $createdCustomer = $result['customer'];

            if (isset($createdCustomer['model']) &&
                $createdCustomer['model'] instanceof \Magento\Customer\Model\Data\Customer) {
                $customerModel = $createdCustomer['model'];

                $customerId = (int)$customerModel->getId();
                if (isset($args['input']['customer_children'])) {
                    $childrenData = $args['input']['customer_children'];
                    foreach ($childrenData as $childData) {
                        $children = $this->childrenFactory->create();
                        $childData['customer_id'] = $customerId;
                        $children->setData($childData);
                        $children->save();
                    }
                }
            }
        }
        return $result;
    }
}
