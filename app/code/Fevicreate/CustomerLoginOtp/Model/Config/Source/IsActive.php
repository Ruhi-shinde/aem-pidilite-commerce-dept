<?php

namespace Fevicreate\CustomerLoginOtp\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * IsActive option for CustomerLoginOtp
 */
class IsActive implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => __('Active')],
            ['value' => 1, 'label' => __('Expired')]
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [1 => __('Expired'), 0 => __('Active')];
    }
}
