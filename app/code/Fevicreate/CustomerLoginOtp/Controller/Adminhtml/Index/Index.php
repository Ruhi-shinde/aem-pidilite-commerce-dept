<?php

namespace Fevicreate\CustomerLoginOtp\Controller\Adminhtml\Index;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action\Context;

/**
 * CustomerLoginOtp Index Controller
 */
class Index extends Action implements HttpGetActionInterface
{
    /**
     * @var PageFactory
     */
    protected PageFactory $resultPageFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Index action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Fevicreate_CustomerLoginOtp::customerloginotp_manage')
            ->addBreadcrumb(__('Customer Login Otp'), __('Customer Login Otp'))
            ->addBreadcrumb(__('Customer Login Otp'), __('Customer Login Otp'));

        $resultPage->getConfig()->getTitle()->prepend(__('Customer Login Otp'));

        return $resultPage;
    }
}
