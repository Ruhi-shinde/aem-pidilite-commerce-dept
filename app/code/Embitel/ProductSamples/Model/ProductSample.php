<?php

declare(strict_types=1);

namespace Embitel\ProductSamples\Model;

use Magento\Framework\Model\AbstractModel;

class ProductSample extends AbstractModel
{
    protected function _construct(): void
    {
        $this->_init(
            \Embitel\ProductSamples\Model\ResourceModel\ProductSample::class
        );
    }
}