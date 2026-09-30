<?php

declare(strict_types=1);

namespace Embitel\ProductSamples\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Embitel\ProductSamples\Model\ProductSampleManagement;

class SaveProductSamples implements ObserverInterface
{
    public function __construct(
        private readonly ProductSampleManagement $productSampleManagement
    ) {
    }

    public function execute(Observer $observer): void
    {
        $product = $observer->getEvent()->getProduct();

        if (!$product || !$product->getId()) {
            return;
        }

        /*
         * If the field wasn't submitted, do nothing.
         * This prevents unrelated product saves from touching our table.
         */
        $rows = $product->getData('custom_product_samples');

        if ($rows === null) {
            return;
        }

        if (!is_array($rows)) {
            $rows = [];
        }

        $this->productSampleManagement->saveRows(
            (int)$product->getId(),
            $rows
        );
    }
}