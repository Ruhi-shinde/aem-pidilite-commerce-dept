<?php

namespace Fevicreate\Customer\Model\ResourceModel;

use Magento\Framework\DB\Select;

/**
 * Otp Resource Model
 */
class Children extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('customer_child', 'child_id');
    }
}
