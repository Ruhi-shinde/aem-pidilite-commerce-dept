<?php
declare(strict_types=1);

namespace Embitel\CustomerSuggestion\Controller\Adminhtml\Suggestion;

use Embitel\CustomerSuggestion\Controller\Adminhtml\Suggestion;
use Magento\Framework\View\Result\PageFactory;

class Index extends Suggestion
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
        $resultPage->getConfig()->getTitle()->prepend(__('Customer Suggestions'));

        return $resultPage;
    }
}
