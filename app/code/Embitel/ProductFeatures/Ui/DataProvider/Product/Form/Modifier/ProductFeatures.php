<?php

declare(strict_types=1);

namespace Embitel\ProductFeatures\Ui\DataProvider\Product\Form\Modifier;

use Embitel\ProductFeatures\Model\FeatureSampleRepository;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Framework\Filesystem;
use Magento\Framework\UrlInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

class ProductFeatures extends AbstractModifier
{
    public function __construct(
        private readonly LocatorInterface $locator,
        private readonly FeatureSampleRepository $repository,
        private readonly UrlInterface $urlBuilder,
        private readonly Filesystem $filesystem
    ) {
    }

    public function modifyData(array $data): array
    {
        $product = $this->locator->getProduct();
        $productId = (int)$product->getId();

        if (!$productId) {
            return $data;
        }

        $samples = $this->repository->getListByProductId($productId);

        $mediaBaseUrl = $this->urlBuilder->getBaseUrl([
            '_type' => UrlInterface::URL_TYPE_MEDIA,
        ]);

        $mediaDirectory = $this->filesystem->getDirectoryRead(
            DirectoryList::MEDIA
        );

        $formSamples = [];

        foreach ($samples as $sample) {
            $file = ltrim(
                trim((string)$sample['file']),
                '/'
            );

            if ($file === '') {
                continue;
            }

            $filePath = $mediaDirectory->getAbsolutePath($file);

            $fileSize = 0;
            $fileType = '';

            if ($mediaDirectory->isFile($file)) {
                $fileSize = (int)filesize($filePath);

                if (function_exists('mime_content_type')) {
                    $mimeType = mime_content_type($filePath);

                    if (is_string($mimeType)) {
                        $fileType = $mimeType;
                    }
                }
            }

            $formSamples[] = [
                'id' => (int)$sample['id'],
                'title' => (string)$sample['title'],
                'sort_order' => (int)$sample['sort_order'],
                'file' => [
                    [
                        'name' => basename($file),
                        'file' => '/' . $file,
                        'url' => $mediaBaseUrl . $file,
                        'size' => $fileSize,
                        'type' => $fileType,
                    ],
                ],
            ];
        }

        $data[$productId][self::DATA_SOURCE_DEFAULT]['custom_product_features'] = $formSamples;

        return $data;
    }

    public function modifyMeta(array $meta): array
    {
        return $meta;
    }
}
