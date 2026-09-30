<?php
/**
 * Copyright © Embitel, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Embitel\ProductPurchase\Model\Product\Attribute;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend;
use Magento\Framework\File\UploaderFactory;
use Magento\Framework\Filesystem;
use Magento\Framework\Exception\LocalizedException;

// Implements backend model to handle file upload and save path to database
class Pdf extends AbstractBackend
{
    /**
     * Pdf save construct
     *
     * @param UploaderFactory $uploaderFactory
     * @param Filesystem $filesystem
     */
    public function __construct(
        protected UploaderFactory $uploaderFactory,
        protected Filesystem $filesystem
    ) {
    }

    /**
     * After save pdf function
     *
     * @param mixed $object
     * @return Pdf
     */
    public function afterSave($object)
    {
        $attributeCode = $this->getAttribute()->getName();

        if (
            !isset($_FILES['product']['name'][$attributeCode]) ||
            empty($_FILES['product']['name'][$attributeCode])
        ) {
            return parent::afterSave($object);
        }

        try {
            // Define Uploader with specific 'fileId' mapping to request data
            $uploader = $this->uploaderFactory->create(['fileId' => 'product[' . $attributeCode . ']']);
            // ... setAllowedExtensions, setFilesDispersion, etc.

            $mediaDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
            $result = $uploader->save($mediaDirectory->getAbsolutePath('product_pdf'));

            if ($result && isset($result['file'])) {
                // Save relative path to EAV table
                $object->setData($attributeCode, 'product_pdf/' . $result['file']);
                $this->getAttribute()->getEntity()->saveAttribute($object, $attributeCode);
            }
        } catch (\Exception $e) {
            throw new LocalizedException(
                __('Unable to save file: %1', $e->getMessage())
            );
        }
        return parent::afterSave($object);
    }
}
