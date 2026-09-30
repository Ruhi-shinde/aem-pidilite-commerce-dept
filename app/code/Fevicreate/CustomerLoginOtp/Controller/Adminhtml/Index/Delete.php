<?php

namespace Fevicreate\CustomerLoginOtp\Controller\Adminhtml\Index;

use Fevicreate\CustomerLoginOtp\Model\Otp;
use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpPostActionInterface;

/**
 * Email Otp Delete Controller
 */
class Delete extends Action implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'Fevicreate_CustomerLoginOtp::customerloginotp_delete';

    /**
     * @var Otp
     */
    protected $otp;

    /**
     * @param Action\Context $context
     * @param Otp $otp
     * @return void
     */
    public function __construct(
        Action\Context $context,
        Otp $otp,
    ) {
        $this->otp = $otp;
        parent::__construct($context);
    }

    /**
     * Delete action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('otp_id');
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($id) {
            try {
                $model = $this->otp;
                $model->load($id);
                $model->delete();

                $this->messageManager->addSuccessMessage(__('The OTP has been deleted.'));

                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/');
            }
        }

        $this->messageManager->addErrorMessage(__('We can\'t find a OTP to delete.'));

        return $resultRedirect->setPath('*/*/');
    }
}
