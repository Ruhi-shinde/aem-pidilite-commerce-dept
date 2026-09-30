<?php
declare(strict_types=1);

namespace Embitel\ContactUs\Model\Resolver;

use Embitel\ContactUs\Model\ResourceModel\ContactUs\CollectionFactory;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class ContactUsList implements ResolverInterface
{
    private CollectionFactory $collectionFactory;

    public function __construct(CollectionFactory $collectionFactory)
    {
        $this->collectionFactory = $collectionFactory;
    }

    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        ?array $value = null,
        ?array $args = null
    ) {
        $pageSize = (int)($args['pageSize'] ?? 20);
        $currentPage = (int)($args['currentPage'] ?? 1);

        if ($pageSize < 1) {
            throw new GraphQlInputException(__('pageSize must be greater than 0.'));
        }

        if ($currentPage < 1) {
            throw new GraphQlInputException(__('currentPage must be greater than 0.'));
        }

        $collection = $this->collectionFactory->create();
        $collection->setOrder('created_at', 'DESC');
        $collection->setOrder('entity_id', 'DESC');
        $collection->setPageSize($pageSize);
        $collection->setCurPage($currentPage);

        $items = [];
        foreach ($collection as $record) {
            $items[] = $this->mapContactUsData($record->getData());
        }

        $totalCount = (int)$collection->getSize();
        $totalPages = $totalCount > 0 ? (int)ceil($totalCount / $pageSize) : 0;

        return [
            'items' => $items,
            'total_count' => $totalCount,
            'page_info' => [
                'page_size' => $pageSize,
                'current_page' => $currentPage,
                'total_pages' => $totalPages
            ]
        ];
    }

    private function mapContactUsData(array $data): array
    {
        return [
            'entity_id' => isset($data['entity_id']) ? (int)$data['entity_id'] : null,
            'first_name' => isset($data['first_name']) ? (string)$data['first_name'] : null,
            'last_name' => isset($data['last_name']) ? (string)$data['last_name'] : null,
            'city' => isset($data['city']) ? (string)$data['city'] : null,
            'email_id' => isset($data['email_id']) ? (string)$data['email_id'] : null,
            'mobile_number' => isset($data['mobile_number']) ? (string)$data['mobile_number'] : null,
            'message' => isset($data['message']) ? (string)$data['message'] : null,
            'created_at' => isset($data['created_at']) ? (string)$data['created_at'] : null,
            'updated_at' => isset($data['updated_at']) ? (string)$data['updated_at'] : null
        ];
    }
}
