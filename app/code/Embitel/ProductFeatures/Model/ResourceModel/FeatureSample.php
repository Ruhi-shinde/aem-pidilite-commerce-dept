<?php

declare(strict_types=1);

namespace Embitel\ProductFeatures\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class FeatureSample extends AbstractDb
{
    protected function _construct(): void
    {
        $this->_init(
            'embitel_downloadable_product_features',
            'entity_id'
        );
    }
}
