<?php
declare(strict_types=1);

namespace Embitel\CustomerSuggestion\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\Page;

abstract class Suggestion extends Action
{
    public const ADMIN_RESOURCE = 'Embitel_CustomerSuggestion::customer_suggestion_view';

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
        $resultPage->setActiveMenu('Embitel_CustomerSuggestion::customer_suggestion_menu')
            ->addBreadcrumb(__('Embitel'), __('Embitel'))
            ->addBreadcrumb(__('Customer Suggestions'), __('Customer Suggestions'));

        return $resultPage;
    }
}
