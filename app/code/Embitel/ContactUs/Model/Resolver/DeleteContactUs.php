<?php
declare(strict_types=1);

namespace Embitel\ContactUs\Model\Resolver;

use Embitel\ContactUs\Api\ContactUsRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class DeleteContactUs implements ResolverInterface
{
    private ContactUsRepositoryInterface $contactUsRepository;

    public function __construct(ContactUsRepositoryInterface $contactUsRepository)
    {
        $this->contactUsRepository = $contactUsRepository;
    }

    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        ?array $value = null,
        ?array $args = null
    ) {
        $entityId = (int)($args['entity_id'] ?? 0);

        if ($entityId <= 0) {
            throw new GraphQlInputException(__('entity_id is required.'));
        }

        try {
            $this->contactUsRepository->deleteById($entityId);
        } catch (NoSuchEntityException $exception) {
            throw new GraphQlInputException(__('Contact us record with entity_id "%1" does not exist.', $entityId));
        }

        return [
            'success' => true,
            'message' => __('Contact us record deleted successfully.')->render()
        ];
    }
}
