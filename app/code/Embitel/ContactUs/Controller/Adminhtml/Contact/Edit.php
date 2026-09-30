<?php
declare(strict_types=1);

namespace Embitel\ContactUs\Controller\Adminhtml\Contact;

use Embitel\ContactUs\Api\ContactUsRepositoryInterface;
use Embitel\ContactUs\Controller\Adminhtml\Contact;
use Embitel\ContactUs\Model\ContactUsFactory;
use Magento\Framework\View\Result\PageFactory;

class Edit extends Contact
{
    private PageFactory $resultPageFactory;
    private ContactUsFactory $contactUsFactory;
    private ContactUsRepositoryInterface $contactUsRepository;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        PageFactory $resultPageFactory,
        ContactUsFactory $contactUsFactory,
        ContactUsRepositoryInterface $contactUsRepository
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->contactUsFactory = $contactUsFactory;
        $this->contactUsRepository = $contactUsRepository;
        parent::__construct($context, $coreRegistry);
    }

    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('entity_id');
        $model = $this->contactUsFactory->create();

        if ($id) {
            try {
                $model = $this->contactUsRepository->get($id);
            } catch (\Exception $exception) {
                $this->messageManager->addErrorMessage(__('This record no longer exists.'));
                return $this->resultRedirectFactory->create()->setPath('*/*/');
            }
        }

        $this->coreRegistry->register('embitel_contact_us', $model);

        $resultPage = $this->resultPageFactory->create();
        $this->initPage($resultPage);
        $resultPage->addBreadcrumb($id ? __('View Contact Request') : __('New Contact Request'), $id ? __('View Contact Request') : __('New Contact Request'));
        $resultPage->getConfig()->getTitle()->prepend(__('Contact Us'));
        $resultPage->getConfig()->getTitle()->prepend($id ? __('Request #%1', $id) : __('New Contact Request'));

        return $resultPage;
    }
}
