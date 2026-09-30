<?php

declare(strict_types=1);

namespace Embitel\ProductFeatures\Model\ResourceModel\FeatureSample;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Embitel\ProductFeatures\Model\FeatureSample;
use Embitel\ProductFeatures\Model\ResourceModel\FeatureSample as FeatureSampleResource;

class Collection extends AbstractCollection
{
    protected function _construct(): void
    {
        $this->_init(
            FeatureSample::class,
            FeatureSampleResource::class
        );
    }
}
