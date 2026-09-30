<?php
declare(strict_types=1);

namespace Embitel\CustomerSuggestion\Model\Resolver;

use Magento\Authorization\Model\UserContextInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Embitel\CustomerSuggestion\Api\CustomerSuggestionRepositoryInterface;
use Embitel\CustomerSuggestion\Model\CustomerSuggestionFactory;

class CreateSuggestion implements ResolverInterface
{
    private CustomerSuggestionFactory $customerSuggestionFactory;
    private CustomerSuggestionRepositoryInterface $customerSuggestionRepository;
    private CustomerRepositoryInterface $customerRepository;

    public function __construct(
        CustomerSuggestionFactory $customerSuggestionFactory,
        CustomerSuggestionRepositoryInterface $customerSuggestionRepository,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->customerSuggestionFactory = $customerSuggestionFactory;
        $this->customerSuggestionRepository = $customerSuggestionRepository;
        $this->customerRepository = $customerRepository;
    }

    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        ?array $value = null,
        ?array $args = null
    ) {
        $input = $args['input'] ?? [];
        $userId = $this->getAuthenticatedCustomerId($context);
        $suggestionText = trim((string)($input['suggestion'] ?? ''));

        if ($suggestionText === '') {
            throw new GraphQlInputException(__('suggestion is required.'));
        }

        try {
            $customer = $this->customerRepository->getById($userId);
        } catch (NoSuchEntityException $exception) {
            throw new GraphQlNoSuchEntityException(__('Customer with id "%1" does not exist.', $userId));
        }

        $userName = trim(
            implode(' ', array_filter([
                (string)$customer->getFirstname(),
                (string)$customer->getLastname()
            ]))
        );

        if ($userName === '') {
            $userName = (string)$customer->getEmail();
        }

        $customerSuggestion = $this->customerSuggestionFactory->create();
        $customerSuggestion->setData([
            'user_id' => $userId,
            'suggestion' => $suggestionText
        ]);

        $this->customerSuggestionRepository->save($customerSuggestion);

        return [
            'entity_id' => (int)$customerSuggestion->getEntityId(),
            'user_id' => (int)$customerSuggestion->getUserId(),
            'user_name' => $userName,
            'user_email' => (string)$customer->getEmail(),
            'suggestion' => (string)$customerSuggestion->getSuggestion(),
            'created_at' => (string)$customerSuggestion->getCreatedAt(),
            'updated_at' => (string)$customerSuggestion->getUpdatedAt()
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
