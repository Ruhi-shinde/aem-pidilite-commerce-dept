<?php

namespace Fevicreate\CustomerLoginOtp\Model\Config\Source;

class Format implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 1, 'label' => __('Number Only')],
            ['value' => 2, 'label' => __('Alpha Only')],
            ['value' => 3, 'label' => __('Alphanumeric')]
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [1 => __('Number Only'), 2 => __('Alpha Only'), 3 => __('Alphanumeric')];
    }
}
