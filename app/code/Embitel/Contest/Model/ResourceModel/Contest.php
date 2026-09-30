<?php
declare(strict_types=1);

namespace Embitel\Contest\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Contest extends AbstractDb
{

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('embitel_contest_contest', 'contest_id');
    }
}

