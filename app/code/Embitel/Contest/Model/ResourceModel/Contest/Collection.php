<?php
declare(strict_types=1);

namespace Embitel\Contest\Model\ResourceModel\Contest;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{

    /**
     * @inheritDoc
     */
    protected $_idFieldName = 'contest_id';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(
            \Embitel\Contest\Model\Contest::class,
            \Embitel\Contest\Model\ResourceModel\Contest::class
        );
    }
}

