<?php

namespace Embitel\CategoryManager\Controller\Adminhtml\Category\Image;

class Upload extends \Magento\Catalog\Controller\Adminhtml\Category\Image\Upload
{
    public const ADMIN_RESOURCE = 'Embitel_CategoryManager::categories';

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(self::ADMIN_RESOURCE);
    }
}