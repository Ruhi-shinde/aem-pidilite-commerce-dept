<?php

namespace Fevicreate\CustomerLoginOtpGraphQl\Model\Resolver;

use Fevicreate\CustomerLoginOtp\Helper\Data as CustomerLoginOtpHelper;
use Fevicreate\CustomerLoginOtp\Model\OtpFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\Validator\EmailAddress as EmailValidator;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;

class SendOTP implements ResolverInterface
{
    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepositoryInterface;

    /**
     * @var OtpFactory
     */
    protected $otpFactory;

    /**
     * @var CustomerLoginOtpHelper
     */
    protected $customerLoginOtpHelper;

    /**
     * @var EmailValidator
     */
    protected $emailValidator;

    /**
     * @var CollectionFactory
     */
    protected $customerCollectionFactory;

    /**
     * @param CustomerRepositoryInterface $customerRepositoryInterface
     * @param OtpFactory $otpFactory
     * @param CustomerLoginOtpHelper $customerLoginOtpHelper
     * @param EmailValidator $emailValidator
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepositoryInterface,
        OtpFactory $otpFactory,
        CustomerLoginOtpHelper $customerLoginOtpHelper,
        EmailValidator $emailValidator,
        CollectionFactory $customerCollectionFactory
    ) {
        $this->customerRepositoryInterface = $customerRepositoryInterface;
        $this->otpFactory = $otpFactory;
        $this->customerLoginOtpHelper = $customerLoginOtpHelper;
        $this->emailValidator = $emailValidator;
        $this->customerCollectionFactory = $customerCollectionFactory;
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

        $phone = $args['phone'] ?? null;
        $email = $args['email'] ?? null;
        $newOTP = "";
        $success = false;
        $tryEmail = false;
        if (empty($email) && empty($phone)) {
            throw new GraphQlInputException(__('Phone or Email is required.'));
        }

        $message = __('There are some issue, please try after some time.');
        try {
            $attribute = "phone_number";
            $attributeValue = $phone;

            if (empty($phone)) {
                $attribute = "email";
                $attributeValue = $email;
            }
            //$customer = $this->customerRepositoryInterface->get($email);
            $collection = $this->customerCollectionFactory->create();
            $collection->addAttributeToSelect(['entity_id','email','phone_number'])
                ->addAttributeToFilter($attribute, $attributeValue);

            if ($websiteId) {
                $collection->addAttributeToFilter('website_id', $websiteId);
            }

            if ($collection->getSize() > 0) {
                $customer = $collection->getFirstItem();
                $message = __("No user found with entered phone number '%1'.", $phone);
                if ($customer && $customer->getId()) {
                    $otp = $this->otpFactory->create();
                    $otp->setCustomerId($customer->getId());
                    $otp->setEmail($customer->getEmail());
                    $otp->setPhone($phone);
                    $otp->setEvent(CustomerLoginOtpHelper::OTP_LOGIN_EVENT);
                    $otp->setWebsiteId($websiteId ?: (int)$customer->getWebsiteId());
                    $newOTP = $this->customerLoginOtpHelper->generateOTP(
                        (int)$customer->getId(),
                        CustomerLoginOtpHelper::OTP_LOGIN_EVENT
                    );
                    $otp->setOtp($newOTP);
                    $otp->save();

                    if ($phone) {
                        $this->customerLoginOtpHelper->sendOtp(
                            $newOTP,
                            $customer->getPhoneNumber(),
                            $store ? (int)$store->getId() : null
                        );
                        $message = __("You will get an OTP on your specific phone number '%1'.", $phone);
                    } else {
                        $this->customerLoginOtpHelper->sendEmailOtp($newOTP, $customer);
                        $message = __("You will get an OTP on your specific email address '%1'.", $email);
                    }
                    $success = true;
                }
            } else {
                $message = __("No user found with entered email address '%1'.", $email);
                if (!empty($phone)) {
                    $tryEmail = true;
                    $message = __("No user found with entered phone number '%1'.", $phone);
                }
            }
        } catch (\Magento\Framework\Exception\MailException $e) {
            $message = $e->getMessage();
        } catch (LocalizedException $e) {
            throw new GraphQlInputException(__($e->getMessage()), $e);
        }

        return [
            'success' => $success,
            'phone' => $phone,
            'email' => $email,
            'try_email' => $tryEmail,
            'otp' => $newOTP,
            'message' => $message,
            'resend_time' => $this->customerLoginOtpHelper->getResendOTPTimeLimit()
        ];
    }
}
