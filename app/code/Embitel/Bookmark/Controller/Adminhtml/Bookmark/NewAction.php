<?php
declare(strict_types=1);

namespace Embitel\Bookmark\Controller\Adminhtml\Bookmark;

class NewAction extends \Embitel\Bookmark\Controller\Adminhtml\Bookmark
{
    protected $resultForwardFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
    ) {
        $this->resultForwardFactory = $resultForwardFactory;
        parent::__construct($context, $coreRegistry);
    }

    public function execute()
    {
        $resultForward = $this->resultForwardFactory->create();
        return $resultForward->forward('edit');
    }
}
