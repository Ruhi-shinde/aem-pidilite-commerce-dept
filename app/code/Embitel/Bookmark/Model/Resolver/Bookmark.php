<?php
declare(strict_types=1);

namespace Embitel\Bookmark\Model\Resolver;

use Embitel\Bookmark\Model\ResourceModel\Bookmark\CollectionFactory;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class Bookmark implements ResolverInterface
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
        if (empty($args['entity_id'])) {
            throw new GraphQlInputException(__('entity_id is required.'));
        }

        $entityId = (int)$args['entity_id'];

        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('entity_id', $entityId);
        $collection->setPageSize(1);

        $bookmark = $collection->getFirstItem();
        if (!$bookmark->getId()) {
            return null;
        }

        return $this->mapBookmarkData($bookmark->getData());
    }

    private function mapBookmarkData(array $data): array
    {
        return [
            'entity_id' => isset($data['entity_id']) ? (string)$data['entity_id'] : null,
            'user_id' => isset($data['user_id']) ? (string)$data['user_id'] : null,
            'bookmark_id' => isset($data['bookmark_id']) ? (string)$data['bookmark_id'] : null,
            'child_id' => isset($data['child_id']) ? (string)$data['child_id'] : null,
            'content_type' => isset($data['content_type']) ? (string)$data['content_type'] : null,
            'content_name' => isset($data['content_name']) ? (string)$data['content_name'] : null,
            'created_at' => isset($data['created_at']) ? (string)$data['created_at'] : null,
            'updated_at' => isset($data['updated_at']) ? (string)$data['updated_at'] : null
        ];
    }
}
