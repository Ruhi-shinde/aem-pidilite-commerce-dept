<?php
declare(strict_types=1);

namespace Embitel\Idealab\Controller\Adminhtml\Idealab;

class Edit extends \Embitel\Idealab\Controller\Adminhtml\Idealab
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
        $id = $this->getRequest()->getParam('idealab_id');
        $model = $this->_objectManager->create(\Embitel\Idealab\Model\Idealab::class);

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This Idea Lab record no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }

        $this->_coreRegistry->register('embitel_idealab_idealab', $model);

        $resultPage = $this->resultPageFactory->create();
        $this->initPage($resultPage)->addBreadcrumb(
            $id ? __('Edit Idea Lab') : __('New Idea Lab'),
            $id ? __('Edit Idea Lab') : __('New Idea Lab')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Idea Labs'));
        $resultPage->getConfig()->getTitle()->prepend($model->getId() ? __('Idea Lab %1', $model->getId()) : __('New Idea Lab'));

        return $resultPage;
    }
}
