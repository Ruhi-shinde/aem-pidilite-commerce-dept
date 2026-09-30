<?php
declare(strict_types=1);

namespace Embitel\ContactUs\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\Page;

abstract class Contact extends Action
{
    public const ADMIN_RESOURCE = 'Embitel_ContactUs::contact_us_view';

    protected Registry $coreRegistry;

    public function __construct(
        Context $context,
        Registry $coreRegistry
    ) {
        $this->coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    protected function initPage(Page $resultPage): Page
    {
        $resultPage->setActiveMenu('Embitel_ContactUs::contact_us_menu')
            ->addBreadcrumb(__('Embitel'), __('Embitel'))
            ->addBreadcrumb(__('Contact Us'), __('Contact Us'));

        return $resultPage;
    }
}
