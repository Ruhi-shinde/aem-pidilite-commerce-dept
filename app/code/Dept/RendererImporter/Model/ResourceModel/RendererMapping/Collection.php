<?php

namespace Dept\RendererImporter\Model\ResourceModel\RendererMapping;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct(): void
    {
        $this->_init('Pidilite\RendererImporter\Model\RendererMapping',
            'Pidilite\RendererImporter\Model\ResourceModel\RendererMapping');
    }
}
