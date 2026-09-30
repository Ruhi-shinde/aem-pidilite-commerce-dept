<?php

namespace Dept\RendererImporter\Model;

use Magento\Framework\Model\AbstractModel;

class RendererMapping extends AbstractModel
{
    protected function _construct(): void {
        $this->_init('Pidilite\RendererImporter\Model\ResourceModel\RendererMapping');
    }

}
