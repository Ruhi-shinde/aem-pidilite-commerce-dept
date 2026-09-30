<?php

namespace Fevicreate\CustomerLoginOtp\Model\ResourceModel;

use Magento\Framework\DB\Select;

/**
 * Otp Resource Model
 */
class Otp extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('customer_login_otp', 'otp_id');
    }
}
