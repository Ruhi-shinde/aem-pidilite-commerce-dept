<?php
declare(strict_types=1);

namespace Embitel\CustomerSuggestion\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Embitel\CustomerSuggestion\Model\ResourceModel\CustomerSuggestion\CollectionFactory;

class Suggestions implements ResolverInterface
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
        foreach ($collection as $suggestion) {
            $items[] = [
                'entity_id' => (int)$suggestion->getData('entity_id'),
                'user_id' => (int)$suggestion->getData('user_id'),
                'user_name' => (string)$suggestion->getData('user_name'),
                'user_email' => (string)$suggestion->getData('user_email'),
                'suggestion' => (string)$suggestion->getData('suggestion'),
                'created_at' => (string)$suggestion->getData('created_at'),
                'updated_at' => (string)$suggestion->getData('updated_at')
            ];
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
}
