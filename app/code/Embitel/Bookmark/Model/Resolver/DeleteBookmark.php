<?php
declare(strict_types=1);

namespace Embitel\Bookmark\Model\Resolver;

use Embitel\Bookmark\Api\BookmarkRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class DeleteBookmark implements ResolverInterface
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
        if (empty($args['entity_id'])) {
            throw new GraphQlInputException(__('entity_id is required.'));
        }

        $entityId = (int)$args['entity_id'];

        try {
            $this->bookmarkRepository->deleteById($entityId);
        } catch (NoSuchEntityException $exception) {
            throw new GraphQlInputException(__('Bookmark with entity_id "%1" does not exist.', $entityId));
        }

        return [
            'success' => true,
            'message' => __('Bookmark deleted successfully.')->render()
        ];
    }
}
