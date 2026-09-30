<?php
declare(strict_types=1);

namespace Embitel\CustomerSuggestion\Controller\Adminhtml\Suggestion;

use Embitel\CustomerSuggestion\Api\CustomerSuggestionRepositoryInterface;
use Embitel\CustomerSuggestion\Model\CustomerSuggestionFactory;
use Magento\Backend\App\Action;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;

class Save extends Action
{
    public const ADMIN_RESOURCE = 'Embitel_CustomerSuggestion::customer_suggestion_save';

    private DataPersistorInterface $dataPersistor;
    private CustomerSuggestionFactory $customerSuggestionFactory;
    private CustomerSuggestionRepositoryInterface $customerSuggestionRepository;
    private CustomerRepositoryInterface $customerRepository;

    public function __construct(
        Action\Context $context,
        DataPersistorInterface $dataPersistor,
        CustomerSuggestionFactory $customerSuggestionFactory,
        CustomerSuggestionRepositoryInterface $customerSuggestionRepository,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->customerSuggestionFactory = $customerSuggestionFactory;
        $this->customerSuggestionRepository = $customerSuggestionRepository;
        $this->customerRepository = $customerRepository;
        parent::__construct($context);
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();

        if (!$data) {
            return $resultRedirect->setPath('*/*/');
        }

        $id = (int)$this->getRequest()->getParam('entity_id');
        $model = $this->customerSuggestionFactory->create();

        if ($id) {
            try {
                $model = $this->customerSuggestionRepository->get($id);
            } catch (\Exception $exception) {
                $this->messageManager->addErrorMessage(__('This customer suggestion no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }
        }

        $userId = (int)($data['user_id'] ?? 0);
        $suggestion = trim((string)($data['suggestion'] ?? ''));

        try {
            if ($userId <= 0) {
                throw new LocalizedException(__('User ID is required.'));
            }
            if ($suggestion === '') {
                throw new LocalizedException(__('Suggestion is required.'));
            }

            $this->customerRepository->getById($userId);

            $model->setData('user_id', $userId);
            $model->setData('suggestion', $suggestion);

            $this->customerSuggestionRepository->save($model);

            $this->messageManager->addSuccessMessage(__('You saved the customer suggestion.'));
            $this->dataPersistor->clear('embitel_customer_suggestion');

            if ($this->getRequest()->getParam('back')) {
                return $resultRedirect->setPath('*/*/edit', ['entity_id' => $model->getEntityId()]);
            }

            return $resultRedirect->setPath('*/*/');
        } catch (LocalizedException $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
        } catch (\Exception $exception) {
            $this->messageManager->addExceptionMessage($exception, __('Something went wrong while saving the customer suggestion.'));
        }

        $this->dataPersistor->set('embitel_customer_suggestion', $data);

        return $resultRedirect->setPath('*/*/edit', ['entity_id' => $id]);
    }
}
