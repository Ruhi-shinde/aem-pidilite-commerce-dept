<?php
namespace Embitel\CategoryManager\Controller\Adminhtml\Category;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Edit extends Action
{
    public const ADMIN_RESOURCE = 'Embitel_CategoryManager::categories';

    /** @var PageFactory */
    private $resultPageFactory;

    /**
     * Initialize the Edit controller with necessary dependencies.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(Context $context, PageFactory $resultPageFactory)
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Execute the action to edit a category in the admin panel.
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $categoryId = (int) $this->getRequest()->getParam('id');
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Embitel_CategoryManager::categories');
        $resultPage->getConfig()->getTitle()->prepend($categoryId ? __('Edit Solution') : __('New Solution'));

        return $resultPage;
    }
}
