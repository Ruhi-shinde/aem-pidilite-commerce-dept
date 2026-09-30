<?php

namespace Embitel\CategoryManager\Block\Adminhtml\Category\Edit\Button;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Framework\UrlInterface;

/**
 * Class Delete button for the category edit form in the admin panel.
 */
class Delete implements ButtonProviderInterface
{
    /**
     * Inject UrlInterface for generating URLs.
     *
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        private UrlInterface $urlBuilder
    ) {
    }

    /**
     * Get button data for the "Delete" button in the category edit form.
     *
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Delete'),
            'class' => 'delete',
            'on_click' => sprintf(
                "deleteConfirm('%s','%s')",
                __('Are you sure you want to delete this solution?'),
                $this->urlBuilder->getUrl(
                    'vse_category/category/delete'
                )
            ),
            'sort_order' => 100
        ];
    }
}
