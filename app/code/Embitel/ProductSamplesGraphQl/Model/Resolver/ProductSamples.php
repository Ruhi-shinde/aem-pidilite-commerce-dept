<?php

declare(strict_types=1);

namespace Embitel\ProductSamplesGraphQl\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\Resolver\BatchRequestItemInterface;
use Magento\Framework\GraphQl\Query\Resolver\BatchResolverInterface;
use Magento\Framework\GraphQl\Query\Resolver\BatchResponse;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\UrlInterface;
use Embitel\ProductSamples\Model\ProductSampleRepository;

class ProductSamples implements BatchResolverInterface
{
    public function __construct(
        private readonly ProductSampleRepository $repository,
        private readonly StoreManagerInterface $storeManager
    ) {
    }

    public function resolve(
        ContextInterface $context,
        Field $field,
        array $requests
    ): BatchResponse {
        $productIds = [];

        foreach ($requests as $request) {
            $value = $request->getValue();

            if (!empty($value['model'])) {
                $productIds[] = (int)$value['model']->getId();
            }
        }

        $samplesByProduct = $this->repository->getListByProductIds(
            $productIds
        );

        $mediaBaseUrl = $this->storeManager
            ->getStore()
            ->getBaseUrl(
                UrlInterface::URL_TYPE_MEDIA
            );

        $response = new BatchResponse();

        foreach ($requests as $request) {
            $value = $request->getValue();

            $productId = !empty($value['model'])
                ? (int)$value['model']->getId()
                : 0;

            $samples = $samplesByProduct[$productId] ?? [];

            $result = [];

            foreach ($samples as $sample) {
                $file = ltrim(
                    (string)$sample['file'],
                    '/'
                );

                $result[] = [
                    'id' => (int)$sample['id'],
                    'title' => (string)$sample['title'],
                    'url' => $mediaBaseUrl . $file,
                    'updated_at'=>(string)$sample['updated_at'],
                    'sort_order' => (int)$sample['sort_order'],
                ];
            }

            $response->addResponse(
                $request,
                $result
            );
        }

        return $response;
    }
}