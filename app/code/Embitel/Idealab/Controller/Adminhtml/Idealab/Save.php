<?php
declare(strict_types=1);

namespace Embitel\Idealab\Controller\Adminhtml\Idealab;

use Magento\Framework\Exception\LocalizedException;

class Save extends \Magento\Backend\App\Action
{
    protected $dataPersistor;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
    ) {
        $this->dataPersistor = $dataPersistor;
        parent::__construct($context);
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            $id = $this->getRequest()->getParam('idealab_id');

            $model = $this->_objectManager->create(\Embitel\Idealab\Model\Idealab::class)->load($id);
            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This Idea Lab record no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }

            if (isset($data['craft_image']) && is_array($data['craft_image'])) {
                $data['craft_image'] = json_encode($data['craft_image'], JSON_UNESCAPED_SLASHES);
            }

            if (!isset($data['i_accept'])) {
                $data['i_accept'] = 0;
            }

            $model->setData($data);

            try {
                $model->save();
                $this->messageManager->addSuccessMessage(__('You saved the Idea Lab record.'));
                $this->dataPersistor->clear('embitel_idealab_idealab');

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['idealab_id' => $model->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Idea Lab record.'));
            }

            $this->dataPersistor->set('embitel_idealab_idealab', $data);
            return $resultRedirect->setPath('*/*/edit', ['idealab_id' => $this->getRequest()->getParam('idealab_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
