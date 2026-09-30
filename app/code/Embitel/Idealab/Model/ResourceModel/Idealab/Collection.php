<?php
declare(strict_types=1);

namespace Embitel\Idealab\Model\ResourceModel\Idealab;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'idealab_id';

    protected function _construct()
    {
        $this->_init(
            \Embitel\Idealab\Model\Idealab::class,
            \Embitel\Idealab\Model\ResourceModel\Idealab::class
        );
    }
}
