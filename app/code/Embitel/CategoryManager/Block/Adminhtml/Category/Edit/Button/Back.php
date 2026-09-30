<?php

namespace Embitel\CategoryManager\Block\Adminhtml\Category\Edit\Button;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Framework\UrlInterface;

/**
 * Class Back button for the category edit form in the admin panel.
 */
class Back implements ButtonProviderInterface
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
     * Get button data for the "Back" button in the category edit form.
     *
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Back'),
            'class' => 'back',
            'on_click' => sprintf(
                "location.href='%s'",
                $this->urlBuilder->getUrl(
                    'vse_category/category/index'
                )
            ),
            'sort_order' => 10
        ];
    }
}
