<?php

namespace Embitel\CategoryManager\Block\Adminhtml\Category\Edit\Button;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class Save button for the category edit form in the admin panel.
 */
class Save implements ButtonProviderInterface
{
    /**
     * Get button data for the "Save Solution" button in the category edit form.
     *
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Save Solution'),
            'class' => 'save primary',
            'data_attribute' => [
                'mage-init' => [
                    'button' => [
                        'event' => 'save'
                    ]
                ]
            ],
            'sort_order' => 90
        ];
    }
}
