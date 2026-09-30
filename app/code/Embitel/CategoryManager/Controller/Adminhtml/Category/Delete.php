<?php
namespace Embitel\CategoryManager\Controller\Adminhtml\Category;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Framework\Controller\Result\Redirect;

class Delete extends Action
{
    public const ADMIN_RESOURCE = 'Embitel_CategoryManager::categories';

    /** @var CategoryRepositoryInterface */
    private $categoryRepository;

    /**
     * Initialize the Delete controller with necessary dependencies.
     *
     * @param Context $context
     * @param CategoryRepositoryInterface $categoryRepository
     */
    public function __construct(
        Context $context,
        CategoryRepositoryInterface $categoryRepository
    ) {
        parent::__construct($context);
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Execute the action to delete a category in the admin panel.
     *
     * @return Redirect
     */
    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $categoryId = (int) $this->getRequest()->getParam('id');

        if (!$categoryId) {
            $this->messageManager->addErrorMessage(__('Category ID is missing.'));
            return $resultRedirect->setPath('*/*/index');
        }

        try {
            $this->categoryRepository->deleteByIdentifier($categoryId);
            $this->messageManager->addSuccessMessage(__('Category deleted successfully.'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Unable to delete category: %1', $e->getMessage()));
        }

        return $resultRedirect->setPath('*/*/index');
    }
}
