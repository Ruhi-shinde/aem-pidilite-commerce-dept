<?php
declare(strict_types=1);

namespace Embitel\Bookmark\Controller\Adminhtml;

abstract class Bookmark extends \Magento\Backend\App\Action
{
    protected $_coreRegistry;
    public const ADMIN_RESOURCE = 'Embitel_Bookmark::bookmark';

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
            ->addBreadcrumb(__('Bookmark'), __('Bookmark'));
        return $resultPage;
    }
}
