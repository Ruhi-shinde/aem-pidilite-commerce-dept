<?php

namespace Embitel\Contest\Controller\Adminhtml\Subscriber;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Newsletter\Controller\Adminhtml\Subscriber as SubscriberAction;

class Index extends SubscriberAction implements HttpGetActionInterface, HttpPostActionInterface
{
    public function execute()
    {
        if ($this->getRequest()->getParam('ajax')) {
            $this->_forward('grid');
            return;
        }

        $this->_view->loadLayout();

        $this->_setActiveMenu('Embitel_Contest::newsletter_subscriber');
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Subscribers'));

        $this->_addBreadcrumb(__('Newsletter'), __('Newsletter'));
        $this->_addBreadcrumb(__('Subscribers'), __('Subscribers'));

        $this->_view->renderLayout();
    }
}
