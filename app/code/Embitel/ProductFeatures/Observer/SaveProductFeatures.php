<?php

declare(strict_types=1);

namespace Embitel\ProductFeatures\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Embitel\ProductFeatures\Model\FeatureSampleManagement;

class SaveProductFeatures implements ObserverInterface
{
    public function __construct(
        private readonly FeatureSampleManagement $featureSampleManagement
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
        $rows = $product->getData('custom_product_features');

        if ($rows === null) {
            return;
        }

        if (!is_array($rows)) {
            $rows = [];
        }

        $this->featureSampleManagement->saveRows(
            (int)$product->getId(),
            $rows
        );
    }
}
