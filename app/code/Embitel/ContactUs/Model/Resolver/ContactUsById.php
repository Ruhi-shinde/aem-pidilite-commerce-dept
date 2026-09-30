<?php
declare(strict_types=1);

namespace Embitel\ContactUs\Model\Resolver;

use Embitel\ContactUs\Api\ContactUsRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class ContactUsById implements ResolverInterface
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
        if (!isset($args['entity_id']) || (int)$args['entity_id'] <= 0) {
            throw new GraphQlInputException(__('entity_id is required.'));
        }

        $entityId = (int)$args['entity_id'];

        try {
            $record = $this->contactUsRepository->get($entityId);
        } catch (NoSuchEntityException $exception) {
            throw new GraphQlInputException(__('Contact us record with entity_id "%1" does not exist.', $entityId));
        }

        return $this->mapContactUsData($record->getData());
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
