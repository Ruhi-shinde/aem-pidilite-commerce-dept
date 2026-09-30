<?php
declare(strict_types=1);

namespace Embitel\Bookmark\Model\Resolver;

use Embitel\Bookmark\Api\BookmarkRepositoryInterface;
use Embitel\Bookmark\Model\BookmarkFactory;
use Magento\Authorization\Model\UserContextInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class CreateBookmark implements ResolverInterface
{
    private BookmarkFactory $bookmarkFactory;
    private BookmarkRepositoryInterface $bookmarkRepository;

    public function __construct(
        BookmarkFactory $bookmarkFactory,
        BookmarkRepositoryInterface $bookmarkRepository
    ) {
        $this->bookmarkFactory = $bookmarkFactory;
        $this->bookmarkRepository = $bookmarkRepository;
    }

    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        ?array $value = null,
        ?array $args = null
    ) {
        $input = $args['input'] ?? [];
        $customerId = $this->getAuthenticatedCustomerId($context);

        if (empty($input['bookmark_id']) || empty($input['content_type']) || empty($input['content_name'])) {
            throw new GraphQlInputException(__('Required fields are missing.'));
        }

        $bookmark = $this->bookmarkFactory->create();
        $bookmark->setData([
            'user_id' => $customerId,
            'bookmark_id' => (string)$input['bookmark_id'],
            'child_id' => isset($input['child_id']) && $input['child_id'] !== '' ? (int)$input['child_id'] : null,
            'content_type' => (string)$input['content_type'],
            'content_name' => (string)$input['content_name']
        ]);

        $this->bookmarkRepository->save($bookmark);

        return [
            'success' => true,
            'message' => __('Bookmark created successfully.')->render(),
            'bookmark' => $this->mapBookmarkData($bookmark->getData())
        ];
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
