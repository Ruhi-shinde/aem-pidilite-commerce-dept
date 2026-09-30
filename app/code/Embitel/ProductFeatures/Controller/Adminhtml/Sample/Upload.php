<?php

declare(strict_types=1);

namespace Embitel\ProductFeatures\Controller\Adminhtml\Sample;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Filesystem;
use Magento\Framework\UrlInterface;
use Magento\MediaStorage\Model\File\UploaderFactory;

class Upload extends Action
{
    private const MEDIA_PATH = 'product_features';

    private const ALLOWED_EXTENSIONS = [
        'pdf',
        'doc',
        'docx',
        'xls',
        'xlsx',
        'ppt',
        'pptx',
        'txt',
        'csv',
        'jpg',
        'jpeg',
        'gif',
        'png',
        'webp',
        'mp4',
        'mov',
        'webm',
        'avi',
        'mkv',
        'zip'
    ];

    public const ADMIN_RESOURCE = 'Embitel_ProductFeatures::product_features';

    public function __construct(
        Context $context,
        private readonly JsonFactory $resultJsonFactory,
        private readonly UploaderFactory $uploaderFactory,
        private readonly Filesystem $filesystem,
        private readonly UrlInterface $urlBuilder
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        $result = $this->resultJsonFactory->create();

        try {
            $file = $this->getUploadedFile();

            if ($file === null) {
                throw new \RuntimeException(
                    'Product sample file was not received.'
                );
            }

            /*
             * Magento's Uploader expects the uploaded file to be
             * available through the configured fileId.
             *
             * The dynamicRows component sends the file as:
             *
             * product[custom_product_features][ROW_INDEX][file]
             *
             * Therefore we temporarily normalize the selected file
             * into $_FILES['file'].
             */
            $_FILES['file'] = $file;

            $mediaDirectory = $this->filesystem->getDirectoryWrite(
                DirectoryList::MEDIA
            );

            $mediaDirectory->create(self::MEDIA_PATH);

            $uploader = $this->uploaderFactory->create(
                ['fileId' => 'file']
            );

            $uploader->setAllowedExtensions(
                self::ALLOWED_EXTENSIONS
            );

            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(false);

            $uploadResult = $uploader->save(
                $mediaDirectory->getAbsolutePath(self::MEDIA_PATH)
            );

            if (!$uploadResult) {
                throw new \RuntimeException(
                    __('File could not be uploaded.')->render()
                );
            }

            $relativePath = self::MEDIA_PATH
                . '/'
                . ltrim((string)$uploadResult['file'], '/');

            $mediaBaseUrl = $this->_url->getBaseUrl(
                ['_type' => UrlInterface::URL_TYPE_MEDIA]
            );

            $uploadResult['file'] = $relativePath;
            $uploadResult['url'] = $mediaBaseUrl . $relativePath;

            return $result->setData($uploadResult);
        } catch (\Throwable $e) {
            return $result
                ->setHttpResponseCode(400)
                ->setData([
                    'error' => true,
                    'message' => $e->getMessage(),
                ]);
        }
    }

    /**
     * Extract the uploaded file from the dynamicRows structure.
     *
     * Expected structure:
     *
     * $_FILES['product']['name']['custom_product_features'][0]['file']
     * $_FILES['product']['name']['custom_product_features'][1]['file']
     * ...
     *
     * @return array<string, mixed>|null
     */
    private function getUploadedFile(): ?array
    {
        if (
            !isset(
                $_FILES['product']['name']['custom_product_features'],
                $_FILES['product']['tmp_name']['custom_product_features'],
                $_FILES['product']['type']['custom_product_features'],
                $_FILES['product']['error']['custom_product_features'],
                $_FILES['product']['size']['custom_product_features']
            )
        ) {
            return null;
        }

        $rows = $_FILES['product']['name']['custom_product_features'];

        if (!is_array($rows)) {
            return null;
        }

        foreach ($rows as $rowIndex => $row) {
            if (
                !is_array($row)
                || !isset($row['file'])
                || $row['file'] === ''
            ) {
                continue;
            }

            return [
                'name' => $_FILES['product']['name']['custom_product_features'][$rowIndex]['file'] ?? '',
                'full_path' => $_FILES['product']['full_path']['custom_product_features'][$rowIndex]['file'] ?? '',
                'type' => $_FILES['product']['type']['custom_product_features'][$rowIndex]['file'] ?? '',
                'tmp_name' => $_FILES['product']['tmp_name']['custom_product_features'][$rowIndex]['file'] ?? '',
                'error' => $_FILES['product']['error']['custom_product_features'][$rowIndex]['file'] ?? UPLOAD_ERR_NO_FILE,
                'size' => $_FILES['product']['size']['custom_product_features'][$rowIndex]['file'] ?? 0,
            ];
        }

        return null;
    }
}
