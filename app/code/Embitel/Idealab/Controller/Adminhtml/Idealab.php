<?php
declare(strict_types=1);

namespace Embitel\Idealab\Controller\Adminhtml;

abstract class Idealab extends \Magento\Backend\App\Action
{
    protected $_coreRegistry;
    public const ADMIN_RESOURCE = 'Embitel_Idealab::idealab';

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    public function initPage($resultPage)
    {
        $resultPage->setActiveMenu(self::ADMIN_RESOURCE)
            ->addBreadcrumb(__('Embitel'), __('Embitel'))
            ->addBreadcrumb(__('Idea Lab'), __('Idea Lab'));
        return $resultPage;
    }
}
