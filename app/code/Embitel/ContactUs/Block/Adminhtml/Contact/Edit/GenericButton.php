<?php
declare(strict_types=1);

namespace Embitel\ContactUs\Block\Adminhtml\Contact\Edit;

use Magento\Backend\Block\Widget\Context;

abstract class GenericButton
{
    protected Context $context;

    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    public function getModelId(): ?int
    {
        $entityId = (int)$this->context->getRequest()->getParam('entity_id');
        return $entityId > 0 ? $entityId : null;
    }

    public function getUrl(string $route = '', array $params = []): string
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
