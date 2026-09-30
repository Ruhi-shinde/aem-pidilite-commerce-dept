<?php

declare(strict_types=1);

namespace Embitel\ProductSamples\Model;

use Embitel\ProductSamples\Model\ResourceModel\ProductSample as ProductSampleResource;
use Embitel\ProductSamples\Model\ResourceModel\ProductSample\CollectionFactory;

class ProductSampleRepository
{
    public function __construct(
        private readonly ProductSampleResource $resource,
        private readonly ProductSampleFactory $productSampleFactory,
        private readonly CollectionFactory $collectionFactory
    ) {
    }

    /**
     * Get all samples for a product.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getListByProductId(int $productId): array
    {
        return $this->getListByProductIds([$productId])[$productId] ?? [];
    }

    /**
     * Get samples for multiple products in one query.
     *
     * @param int[] $productIds
     * @return array<int, array<int, array<string, mixed>>>
     */
    public function getListByProductIds(array $productIds): array
    {
        $productIds = array_values(
            array_unique(
                array_filter(
                    array_map('intval', $productIds)
                )
            )
        );

        if (!$productIds) {
            return [];
        }

        $collection = $this->collectionFactory->create();

        $collection->addFieldToFilter(
            'product_id',
            ['in' => $productIds]
        );

        $collection->setOrder('sort_order', 'ASC');
        $collection->setOrder('entity_id', 'ASC');

        $result = [];

        foreach ($collection as $sample) {
            $productId = (int)$sample->getData('product_id');

            $result[$productId][] = [
                'id' => (int)$sample->getId(),
                'title' => (string)$sample->getData('title'),
                'file' => (string)$sample->getData('file'),
                'updated_at' => (string)$sample->getData('updated_at'),
                'sort_order' => (int)$sample->getData('sort_order'),
            ];
        }

        return $result;
    }

    public function create(): ProductSample
    {
        return $this->productSampleFactory->create();
    }

    public function save(ProductSample $sample): ProductSample
    {
        $this->resource->save($sample);

        return $sample;
    }

    public function delete(ProductSample $sample): void
    {
        $this->resource->delete($sample);
    }
}