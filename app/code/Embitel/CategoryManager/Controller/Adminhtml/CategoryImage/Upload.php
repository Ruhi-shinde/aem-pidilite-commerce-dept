<?php
namespace Embitel\CategoryManager\Controller\Adminhtml\CategoryImage;

use Magento\Backend\App\Action\Context;
use Magento\Catalog\Model\ImageUploader;

/**
 * Controller class for handling category image uploads in the admin panel.
 */
class Upload extends \Magento\Catalog\Controller\Adminhtml\Category\Image\Upload
{
    public const ADMIN_RESOURCE = 'Embitel_CategoryManager::categories';

    /**
     * Check if the user has permission to access this controller action.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(self::ADMIN_RESOURCE);
    }
}
