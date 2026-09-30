<?php
declare(strict_types=1);

namespace Embitel\ContactUs\Controller\Adminhtml\Contact;

use Embitel\ContactUs\Controller\Adminhtml\Contact;
use Magento\Framework\View\Result\PageFactory;

class Index extends Contact
{
    private PageFactory $resultPageFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context, $coreRegistry);
    }

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $this->initPage($resultPage);
        $resultPage->getConfig()->getTitle()->prepend(__('Contact Us'));

        return $resultPage;
    }
}
