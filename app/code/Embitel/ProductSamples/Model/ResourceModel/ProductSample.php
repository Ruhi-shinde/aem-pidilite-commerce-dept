<?php

declare(strict_types=1);

namespace Embitel\ProductSamples\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class ProductSample extends AbstractDb
{
    protected function _construct(): void
    {
        $this->_init(
            'embitel_downloadable_product_sample',
            'entity_id'
        );
    }
}