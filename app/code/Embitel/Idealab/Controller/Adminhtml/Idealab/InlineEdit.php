<?php
declare(strict_types=1);

namespace Embitel\Idealab\Controller\Adminhtml\Idealab;

class InlineEdit extends \Magento\Backend\App\Action
{
    protected $jsonFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
    ) {
        parent::__construct($context);
        $this->jsonFactory = $jsonFactory;
    }

    public function execute()
    {
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];

        if ($this->getRequest()->getParam('isAjax')) {
            $postItems = $this->getRequest()->getParam('items', []);
            if (!count($postItems)) {
                $messages[] = __('Please correct the data sent.');
                $error = true;
            } else {
                foreach (array_keys($postItems) as $modelid) {
                    $model = $this->_objectManager->create(\Embitel\Idealab\Model\Idealab::class)->load($modelid);
                    try {
                        $updateData = array_merge($model->getData(), $postItems[$modelid]);
                        if (isset($updateData['craft_image']) && is_array($updateData['craft_image'])) {
                            $updateData['craft_image'] = json_encode($updateData['craft_image'], JSON_UNESCAPED_SLASHES);
                        }
                        $model->setData($updateData);
                        $model->save();
                    } catch (\Exception $e) {
                        $messages[] = "[Idea Lab ID: {$modelid}] {$e->getMessage()}";
                        $error = true;
                    }
                }
            }
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }
}
