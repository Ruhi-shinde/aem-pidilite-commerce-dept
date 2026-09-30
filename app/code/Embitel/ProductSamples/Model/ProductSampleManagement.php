<?php

declare(strict_types=1);

namespace Embitel\ProductSamples\Model;

use Magento\Framework\Exception\LocalizedException;

class ProductSampleManagement
{
    public function __construct(
        private readonly ProductSampleRepository $repository
    ) {}

    /**
     * Save all submitted rows for a product.
     *
     * @param int $productId
     * @param array $rows
     * @return void
     * @throws LocalizedException
     */
    public function saveRows(int $productId, array $rows): void
    {
        $existingRows = $this->repository->getListByProductId($productId);

        $existingById = [];

        foreach ($existingRows as $row) {
            $existingById[(int)$row['id']] = $row;
        }

        $submittedIds = [];

        foreach ($rows as $row) {
            if (!is_array($row)) {
                continue;
            }

            $id = (int)($row['id'] ?? 0);

            if (!empty($row['is_delete'])) {
                continue;
            }

            $title = trim((string)($row['title'] ?? ''));
            $file = $this->extractFilePath($row['file'] ?? null);

            if ($title === '' && $file === '') {
                continue;
            }

            if ($title === '') {
                throw new LocalizedException(
                    __('Product sample title is required.')
                );
            }

            if ($file === '') {
                throw new LocalizedException(
                    __('Please upload a file for "%1".', $title)
                );
            }

            if ($id && !isset($existingById[$id])) {
                throw new LocalizedException(
                    __('Invalid product sample ID "%1".', $id)
                );
            }

            $sample = $id
                ? $this->repository->create()->load($id)
                : $this->repository->create();

            if ($id && (int)$sample->getData('product_id') !== $productId) {
                throw new LocalizedException(
                    __('Invalid product sample.')
                );
            }

            $sample->setData('product_id', $productId);
            $sample->setData('title', $title);
            $sample->setData('file', $file);
            $sample->setData(
                'sort_order',
                (int)($row['sort_order'] ?? 0)
            );

            $this->repository->save($sample);

            $submittedIds[] = (int)$sample->getId();
        }

        /*
         * Delete records that were removed from the UI.
         */
        foreach ($existingById as $existingId => $existingRow) {
            if (!in_array($existingId, $submittedIds, true)) {
                $sample = $this->repository->create()
                    ->load($existingId);

                if (
                    $sample->getId()
                    && (int)$sample->getData('product_id') === $productId
                ) {
                    $this->repository->delete($sample);
                }
            }
        }
    }

    private function extractFilePath(mixed $file): string
    {
        if (is_string($file)) {
            return ltrim(trim($file), '/');
        }

        if (!is_array($file)) {
            return '';
        }

        // Standard uploader response:
        // ['file' => 'product_samples/example.pdf']
        if (!empty($file['file']) && is_string($file['file'])) {
            return ltrim(trim($file['file']), '/');
        }

        // Alternative structure:
        // ['path' => 'product_samples/example.pdf']
        if (!empty($file['path']) && is_string($file['path'])) {
            return ltrim(trim($file['path']), '/');
        }

        // FileUploader may submit:
        // [
        //     0 => [
        //         'name' => 'example.pdf',
        //         'file' => 'product_samples/example.pdf',
        //         'url' => '...'
        //     ]
        // ]
        foreach ($file as $item) {
            if (!is_array($item)) {
                continue;
            }

            if (!empty($item['file']) && is_string($item['file'])) {
                return ltrim(trim($item['file']), '/');
            }

            if (!empty($item['path']) && is_string($item['path'])) {
                return ltrim(trim($item['path']), '/');
            }
        }

        return '';
    }
}
