<?php

namespace Fevicreate\CustomerLoginOtpGraphQl\Model\Resolver;

use Fevicreate\CustomerLoginOtp\Helper\Data as CustomerLoginOtpHelper;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Integration\Model\Oauth\TokenFactory;

class ValidateOtp implements ResolverInterface
{
    /**
     * @var CustomerLoginOtpHelper
     */
    protected $customerLoginOtpHelper;

    /**
     * @var CollectionFactory
     */
    protected $customerCollectionFactory;

    /**
     * @var TokenFactory
     */
    protected $tokenModelFactory;

    /**
     * @param CustomerLoginOtpHelper $customerLoginOtpHelper
     * @param CollectionFactory $customerCollectionFactory
     * @param TokenFactory $tokenModelFactory
     */
    public function __construct(
        CustomerLoginOtpHelper $customerLoginOtpHelper,
        CollectionFactory $customerCollectionFactory,
        TokenFactory $tokenModelFactory
    ) {
        $this->customerLoginOtpHelper = $customerLoginOtpHelper;
        $this->customerCollectionFactory = $customerCollectionFactory;
        $this->tokenModelFactory = $tokenModelFactory;
    }

    /**
     * @inheritdoc
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        ?array $value = null,
        ?array $args = null
    ) {
        $store = $context->getExtensionAttributes()->getStore();
        $websiteId = $store ? (int)$store->getWebsiteId() : null;

        /*if (!isset($args['phone']) || (isset($args['phone']) && empty($args['phone']))) {
            throw new GraphQlInputException(__('"Phone number must be specified'));
        }*/

        $phone = $args['phone'] ?? null;
        $email = $args['email'] ?? null;

        if (empty($email) && empty($phone)) {
            throw new GraphQlInputException(__('Phone or Email is required.'));
        }

        if (!isset($args['otp']) || (isset($args['otp']) && empty($args['otp']))) {
            throw new GraphQlInputException(__('"OTP must be specified'));
        }

        $success = false;
        $message = __('OTP is not valid.');
        $customer = [];
        try {
            $attribute = "phone_number";
            $attributeValue = $phone;
            $otpValidationOn = "phone";
            if (empty($phone)) {
                $attribute = "email";
                $attributeValue = $email;
                $otpValidationOn = "email";
            }

            $otp = $args['otp'];
            //$phone = $args['phone'];
            $event = $args['event'] ?? customerLoginOtpHelper::OTP_LOGIN_EVENT;

            if ($this->customerLoginOtpHelper->validateOTP($otp, $attributeValue, $otpValidationOn, $event, $websiteId)) {
                $collection = $this->customerCollectionFactory->create();
                $collection->addAttributeToSelect('*')
                    ->addAttributeToFilter($attribute, $attributeValue);

                if ($websiteId) {
                    $collection->addAttributeToFilter('website_id', $websiteId);
                }

                $customer = $collection->getFirstItem();

                if (!$customer || !$customer->getId()) {
                    throw new GraphQlInputException(__('No user found in the requested store website.'));
                }

                $tokenModel = $this->tokenModelFactory->create();
                $token = $tokenModel->createCustomerToken($customer->getId())->getToken();

                $success = true;
                $message = __('OTP validated successfully.');
            } else {
                throw new GraphQlInputException(__('OTP is not valid.'));
            }
        } catch (LocalizedException $e) {
            throw new GraphQlInputException(__($e->getMessage()), $e);
        }

        return [
            'phone' => $phone,
            'email' => $email,
            'success' => $success,
            'message' => $message,
            'customer' => $customer,
            'token' => $token
        ];
    }
}
