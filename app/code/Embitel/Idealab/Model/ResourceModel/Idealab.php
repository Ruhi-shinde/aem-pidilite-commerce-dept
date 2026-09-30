<?php
declare(strict_types=1);

namespace Embitel\Idealab\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Idealab extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('embitel_idealab_idealab', 'idealab_id');
    }
}
