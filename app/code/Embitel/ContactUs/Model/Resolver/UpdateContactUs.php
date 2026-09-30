<?php
declare(strict_types=1);

namespace Embitel\ContactUs\Model\Resolver;

use Embitel\ContactUs\Api\ContactUsRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class UpdateContactUs implements ResolverInterface
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
        $input = $args['input'] ?? [];
        $entityId = (int)($input['entity_id'] ?? 0);

        if ($entityId <= 0) {
            throw new GraphQlInputException(__('entity_id is required.'));
        }

        try {
            $contactUs = $this->contactUsRepository->get($entityId);
        } catch (NoSuchEntityException $exception) {
            throw new GraphQlInputException(__('Contact us record with entity_id "%1" does not exist.', $entityId));
        }

        $currentData = $contactUs->getData();
        $updateData = [];

        foreach (['first_name', 'last_name', 'city', 'email_id', 'mobile_number', 'message'] as $fieldName) {
            if (array_key_exists($fieldName, $input)) {
                $updateData[$fieldName] = trim((string)$input[$fieldName]);
            }
        }

        if (isset($updateData['first_name']) && $updateData['first_name'] === '') {
            throw new GraphQlInputException(__('first_name cannot be empty.'));
        }

        if (isset($updateData['last_name']) && $updateData['last_name'] === '') {
            throw new GraphQlInputException(__('last_name cannot be empty.'));
        }

        if (isset($updateData['city']) && $updateData['city'] === '') {
            throw new GraphQlInputException(__('city cannot be empty.'));
        }

        if (isset($updateData['email_id']) && $updateData['email_id'] === '') {
            throw new GraphQlInputException(__('email_id cannot be empty.'));
        }

        if (isset($updateData['mobile_number']) && $updateData['mobile_number'] === '') {
            throw new GraphQlInputException(__('mobile_number cannot be empty.'));
        }

        if (isset($updateData['message']) && $updateData['message'] === '') {
            throw new GraphQlInputException(__('message cannot be empty.'));
        }

        $contactUs->setData(array_merge($currentData, $updateData));
        $this->contactUsRepository->save($contactUs);

        return $this->mapContactUsData($contactUs->getData());
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
