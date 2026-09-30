<?php

namespace Dept\RendererImporter\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class RendererMapping extends AbstractDb
{
    protected function _construct(): void
    {
        $this->_init('custom_shade_to_renderer_mapping_tbl', 'entity_id');
    }
}
