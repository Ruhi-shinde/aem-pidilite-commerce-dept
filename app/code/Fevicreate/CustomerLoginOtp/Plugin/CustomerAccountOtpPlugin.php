<?php

namespace Fevicreate\CustomerLoginOtp\Plugin;

use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Fevicreate\CustomerLoginOtp\Model\OtpFactory;
use Fevicreate\CustomerLoginOtp\Helper\Data as CustomerLoginOtpHelper;

class CustomerAccountOtpPlugin
{
    /**
     * @var RequestInterface
     */
    private $request;

    private $customerFactory;

    private $otpFactory;

    private $customerLoginOtpHelper;

    /**
     * @param RequestInterface $request
     */
    public function __construct(
        RequestInterface $request,
        CustomerFactory $customerFactory,
        OtpFactory $otpFactory,
        CustomerLoginOtpHelper $customerLoginOtpHelper,
    ) {
        $this->request = $request;
        $this->customerFactory = $customerFactory;
        $this->otpFactory = $otpFactory;
        $this->customerLoginOtpHelper = $customerLoginOtpHelper;
    }

    /**
     * @param \Magento\CustomerGraphQl\Model\Resolver\CreateCustomer $subject
     * @param $result
     * @param Field $field
     * @param $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return mixed
     * @throws \Exception
     */
    public function afterResolve(
        \Magento\CustomerGraphQl\Model\Resolver\CreateCustomer $subject,
        $result,
        Field $field,
        $context,
        ResolveInfo $info,
        ?array $value = null,
        ?array $args = null
    ) {
        if (isset($result['customer'])) {
            $createdCustomer = $result['customer'];

            if (isset($createdCustomer['model']) &&
                $createdCustomer['model'] instanceof \Magento\Customer\Model\Data\Customer) {
                $customerModel = $createdCustomer['model'];

                $customerId = (int)$customerModel->getId();
                $customer = $this->customerFactory->create()->load($customerId);
                $otp = $this->otpFactory->create();
                $otp->setCustomerId($customer->getId());
                $otp->setEmail($customer->getEmail());
                $otp->setPhone($customer->getPhoneNumber());
                $otp->setEvent(CustomerLoginOtpHelper::OTP_REGISTRATION_EVENT);
                $otp->setWebsiteId($customer->getWebsiteId());
                $newOTP = $this->customerLoginOtpHelper->generateOTP((int) $customer->getId());
                $otp->setOtp($newOTP);

                $otp->save();
            }
        }
        return $result;
    }
}
