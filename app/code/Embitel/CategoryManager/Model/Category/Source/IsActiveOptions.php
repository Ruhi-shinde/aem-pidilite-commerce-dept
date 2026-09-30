<?php

namespace Embitel\CategoryManager\Model\Category\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Provides options for the 'is_active' attribute in categories.
 */
class IsActiveOptions implements OptionSourceInterface
{
    /**
     * Provide options for the 'is_active' attribute in categories.
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => '1', 'label' => __('Enabled')],
            ['value' => '0', 'label' => __('Disabled')],
        ];
    }
}
