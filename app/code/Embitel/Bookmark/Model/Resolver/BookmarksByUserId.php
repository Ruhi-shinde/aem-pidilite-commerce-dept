<?php
declare(strict_types=1);

namespace Embitel\Bookmark\Model\Resolver;

use Embitel\Bookmark\Model\ResourceModel\Bookmark\CollectionFactory;
use Magento\Authorization\Model\UserContextInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class BookmarksByUserId implements ResolverInterface
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
        $userId = $this->getAuthenticatedCustomerId($context);
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('user_id', $userId);
        $collection->setOrder('created_at', 'DESC');

        $result = [];
        foreach ($collection as $bookmark) {
            $result[] = $this->mapBookmarkData($bookmark->getData());
        }

        return $result;
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

    private function getAuthenticatedCustomerId($context): int
    {
        $customerId = (int)$context->getUserId();
        $userType = (int)$context->getUserType();

        if ($customerId <= 0 || $userType !== UserContextInterface::USER_TYPE_CUSTOMER) {
            throw new GraphQlAuthorizationException(
                __('Customer token is missing or invalid.')
            );
        }

        return $customerId;
    }
}
