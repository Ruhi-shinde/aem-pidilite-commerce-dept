<?php

namespace Fevicreate\Customer\Plugin;

use Magento\Framework\App\RequestInterface;
use Fevicreate\Customer\Model\ChildrenFactory;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;

class CustomerAccountChildPlugin
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
        if (isset($args['input']) && $args['input']['custom_attributes']) {
            foreach ($args['input']['custom_attributes'] as $key => $attribute) {
                if ($attribute['attribute_code'] == 'phone_number') {
                    $phone = $attribute['value'];
                    break;
                }
            }
        }

        if ($phone == "") {
            throw new GraphQlInputException(__('phone number value should be specified'));
        }

        $collection = $this->customerCollectionFactory->create();
        $collection->addAttributeToSelect('*')
            ->addAttributeToFilter("phone_number", $phone)
            ->addAttributeToFilter("website_id", $websiteId);

        if ($collection->getSize() > 0) {
            throw new GraphQlInputException(
                __('A customer with the same phone number already exists in an associated website.')
            );
        }

        return [$field, $context, $info, $value, $args];
    }

    /**
     * @param \Magento\CustomerGraphQl\Model\Resolver\CreateCustomer $subject
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
        \Magento\CustomerGraphQl\Model\Resolver\CreateCustomer $subject,
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
