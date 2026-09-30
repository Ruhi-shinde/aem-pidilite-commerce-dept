<?php

declare(strict_types=1);

namespace Embitel\ProductSamples\Model\ResourceModel\ProductSample;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Embitel\ProductSamples\Model\ProductSample;
use Embitel\ProductSamples\Model\ResourceModel\ProductSample as ProductSampleResource;

class Collection extends AbstractCollection
{
    protected function _construct(): void
    {
        $this->_init(
            ProductSample::class,
            ProductSampleResource::class
        );
    }
}