<?php
declare(strict_types=1);

namespace Embitel\Bookmark\Model\Resolver;

use Embitel\Bookmark\Api\BookmarkRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class UpdateBookmark implements ResolverInterface
{
    private BookmarkRepositoryInterface $bookmarkRepository;

    public function __construct(BookmarkRepositoryInterface $bookmarkRepository)
    {
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

        if (empty($input['entity_id'])) {
            throw new GraphQlInputException(__('entity_id is required.'));
        }

        $entityId = (int)$input['entity_id'];

        try {
            $bookmark = $this->bookmarkRepository->get($entityId);
        } catch (NoSuchEntityException $exception) {
            throw new GraphQlInputException(__('Bookmark with entity_id "%1" does not exist.', $entityId));
        }

        $currentData = $bookmark->getData();
        $updateData = [];
        foreach (['user_id', 'bookmark_id', 'child_id', 'content_type', 'content_name'] as $fieldName) {
            if (array_key_exists($fieldName, $input)) {
                $updateData[$fieldName] = $input[$fieldName];
            }
        }

        if (array_key_exists('user_id', $updateData)) {
            $updateData['user_id'] = (int)$updateData['user_id'];
        }

        if (array_key_exists('child_id', $updateData)) {
            $updateData['child_id'] = $updateData['child_id'] !== null && $updateData['child_id'] !== ''
                ? (int)$updateData['child_id']
                : null;
        }

        $bookmark->setData(array_merge($currentData, $updateData));
        $this->bookmarkRepository->save($bookmark);

        return [
            'success' => true,
            'message' => __('Bookmark updated successfully.')->render(),
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
}
