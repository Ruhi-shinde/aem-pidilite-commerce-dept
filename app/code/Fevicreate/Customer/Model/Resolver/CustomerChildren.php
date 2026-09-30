<?php

namespace Fevicreate\Customer\Model\Resolver;

use Fevicreate\Customer\Model\ResourceModel\Children\Collection as ChildrenCollection;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Query\ResolverInterface;

class CustomerChildren implements ResolverInterface
{
    /**
     * @var ChildrenCollection
     */
    protected $childrenCollection;

    /**
     * @param ChildrenCollection $childrenCollection
     */
    public function __construct(
        ChildrenCollection $childrenCollection
    ) {
        $this->childrenCollection = $childrenCollection;
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
        if (!isset($value['model'])) {
            return null;
        }

        /** @var \Magento\Customer\Model\Customer $customerModel */
        $customerModel = $value['model'];

        $collection = $this->childrenCollection->addFieldToFilter('customer_id', $customerModel->getId());
        $output = [];
        if ($collection) {
            foreach ($collection as $item) {
                $output[] = [
                'child_id' => $item->getId(),
                    'name' => $item->getName(),
                    'date_of_birth' => $item->getDateOfBirth(),
                    'school'          => $item->getSchool()
                ];
            }
        }

        return $output;
    }
}
