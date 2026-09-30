<?php

namespace Fevicreate\CustomerLoginOtp\Model;

/**
 * Otp Model
 *
 * @method \Fevicreate\CustomerLoginOtp\Model\Resource\Otp _getResource()
 * @method \Fevicreate\CustomerLoginOtp\Model\Resource\Otp getResource()
 */
class Otp extends \Magento\Framework\Model\AbstractModel
{
    public const EMAIL_OTP_TABLE_NAME = 'customer_login_otp';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Fevicreate\CustomerLoginOtp\Model\ResourceModel\Otp::class);
    }
}
