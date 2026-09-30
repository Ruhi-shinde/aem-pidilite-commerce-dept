<?php
declare(strict_types=1);

namespace Embitel\Bookmark\Controller\Adminhtml\Bookmark;

class Edit extends \Embitel\Bookmark\Controller\Adminhtml\Bookmark
{
    protected $resultPageFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context, $coreRegistry);
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('entity_id');
        $model = $this->_objectManager->create(\Embitel\Bookmark\Model\Bookmark::class);

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This Bookmark record no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }

        $this->_coreRegistry->register('embitel_bookmark_bookmark', $model);

        $resultPage = $this->resultPageFactory->create();
        $this->initPage($resultPage)->addBreadcrumb(
            $id ? __('Edit Bookmark') : __('New Bookmark'),
            $id ? __('Edit Bookmark') : __('New Bookmark')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Bookmarks'));
        $resultPage->getConfig()->getTitle()->prepend($model->getId() ? __('Bookmark %1', $model->getId()) : __('New Bookmark'));

        return $resultPage;
    }
}
