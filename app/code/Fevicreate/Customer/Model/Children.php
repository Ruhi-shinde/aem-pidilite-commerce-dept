<?php

namespace Fevicreate\Customer\Model;

/**
 * Children class
 */
class Children extends \Magento\Framework\Model\AbstractModel
{
    public const EMAIL_OTP_TABLE_NAME = 'customer_child';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Fevicreate\Customer\Model\ResourceModel\Children::class);
    }
}
