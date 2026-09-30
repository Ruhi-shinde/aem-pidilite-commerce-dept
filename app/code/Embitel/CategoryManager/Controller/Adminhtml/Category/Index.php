<?php
namespace Embitel\CategoryManager\Controller\Adminhtml\Category;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    public const ADMIN_RESOURCE = 'Embitel_CategoryManager::categories';

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * Initialize the Index controller with necessary dependencies.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Execute the action to display the category management page in the admin panel.
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Embitel_CategoryManager::categories');
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Solutions'));

        return $resultPage;
    }
}
