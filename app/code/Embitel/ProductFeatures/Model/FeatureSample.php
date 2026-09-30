<?php

declare(strict_types=1);

namespace Embitel\ProductFeatures\Model;

use Magento\Framework\Model\AbstractModel;

class FeatureSample extends AbstractModel
{
    protected function _construct(): void
    {
        $this->_init(
            \Embitel\ProductFeatures\Model\ResourceModel\FeatureSample::class
        );
    }
}
