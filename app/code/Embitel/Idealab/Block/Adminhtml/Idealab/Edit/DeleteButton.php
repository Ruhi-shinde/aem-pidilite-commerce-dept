<?php
declare(strict_types=1);

namespace Embitel\Idealab\Block\Adminhtml\Idealab\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class DeleteButton extends GenericButton implements ButtonProviderInterface
{
    public function getButtonData()
    {
        $data = [];
        if ($this->getModelId()) {
            $data = [
                'label' => __('Delete Idea Lab'),
                'class' => 'delete',
                'on_click' => 'deleteConfirm(\'' . __('Are you sure you want to do this?') . '\', \'' . $this->getDeleteUrl() . '\')',
                'sort_order' => 20,
            ];
        }

        return $data;
    }

    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', ['idealab_id' => $this->getModelId()]);
    }
}
