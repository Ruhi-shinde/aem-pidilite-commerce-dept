<?php
declare(strict_types=1);

namespace Embitel\CustomerSuggestion\Controller\Adminhtml\Suggestion;

use Embitel\CustomerSuggestion\Api\CustomerSuggestionRepositoryInterface;
use Embitel\CustomerSuggestion\Controller\Adminhtml\Suggestion;
use Embitel\CustomerSuggestion\Model\CustomerSuggestionFactory;
use Magento\Framework\View\Result\PageFactory;

class Edit extends Suggestion
{
    private PageFactory $resultPageFactory;
    private CustomerSuggestionFactory $customerSuggestionFactory;
    private CustomerSuggestionRepositoryInterface $customerSuggestionRepository;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        PageFactory $resultPageFactory,
        CustomerSuggestionFactory $customerSuggestionFactory,
        CustomerSuggestionRepositoryInterface $customerSuggestionRepository
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->customerSuggestionFactory = $customerSuggestionFactory;
        $this->customerSuggestionRepository = $customerSuggestionRepository;
        parent::__construct($context, $coreRegistry);
    }

    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('entity_id');
        $model = $this->customerSuggestionFactory->create();

        if ($id) {
            try {
                $model = $this->customerSuggestionRepository->get($id);
            } catch (\Exception $exception) {
                $this->messageManager->addErrorMessage(__('This customer suggestion no longer exists.'));
                return $this->resultRedirectFactory->create()->setPath('*/*/');
            }
        }

        $this->coreRegistry->register('embitel_customer_suggestion', $model);

        $resultPage = $this->resultPageFactory->create();
        $this->initPage($resultPage);
        $resultPage->addBreadcrumb($id ? __('Edit Suggestion') : __('New Suggestion'), $id ? __('Edit Suggestion') : __('New Suggestion'));
        $resultPage->getConfig()->getTitle()->prepend(__('Customer Suggestions'));
        $resultPage->getConfig()->getTitle()->prepend($id ? __('Suggestion #%1', $id) : __('New Suggestion'));

        return $resultPage;
    }
}
