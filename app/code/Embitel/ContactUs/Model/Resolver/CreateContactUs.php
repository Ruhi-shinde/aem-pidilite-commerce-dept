<?php
declare(strict_types=1);

namespace Embitel\ContactUs\Model\Resolver;

use Embitel\ContactUs\Api\ContactUsRepositoryInterface;
use Embitel\ContactUs\Model\ContactUsFactory;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class CreateContactUs implements ResolverInterface
{
    private ContactUsFactory $contactUsFactory;
    private ContactUsRepositoryInterface $contactUsRepository;

    public function __construct(
        ContactUsFactory $contactUsFactory,
        ContactUsRepositoryInterface $contactUsRepository
    ) {
        $this->contactUsFactory = $contactUsFactory;
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

        $firstName = trim((string)($input['first_name'] ?? ''));
        $lastName = trim((string)($input['last_name'] ?? ''));
        $city = trim((string)($input['city'] ?? ''));
        $emailId = trim((string)($input['email_id'] ?? ''));
        $mobileNumber = trim((string)($input['mobile_number'] ?? ''));
        $message = trim((string)($input['message'] ?? ''));

        if ($firstName === '') {
            throw new GraphQlInputException(__('first_name is required.'));
        }

        if ($lastName === '') {
            throw new GraphQlInputException(__('last_name is required.'));
        }

        if ($city === '') {
            throw new GraphQlInputException(__('city is required.'));
        }

        if ($emailId === '') {
            throw new GraphQlInputException(__('email_id is required.'));
        }

        if ($mobileNumber === '') {
            throw new GraphQlInputException(__('mobile_number is required.'));
        }

        if ($message === '') {
            throw new GraphQlInputException(__('message is required.'));
        }

        $contactUs = $this->contactUsFactory->create();
        $contactUs->setData([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'city' => $city,
            'email_id' => $emailId,
            'mobile_number' => $mobileNumber,
            'message' => $message
        ]);

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
