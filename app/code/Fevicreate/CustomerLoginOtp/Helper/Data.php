<?php

namespace Fevicreate\CustomerLoginOtp\Helper;

use Fevicreate\CustomerLoginOtp\Helper\Data as CustomerLoginOtpHelper;
use Fevicreate\CustomerLoginOtp\Model\Otp;
use Fevicreate\CustomerLoginOtp\Model\ResourceModel\Otp\CollectionFactory as OtpCollectionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Escaper;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;

/**
 * Helper class for Login Email OTP
 */
class Data extends AbstractHelper
{
    public const OTP_REGISTRATION_EVENT = "registration";

    public const OTP_LOGIN_EVENT = "login";

    public const XML_PATH_CUSTOMER_LOGIN_OTP_FORMAT = 'customerloginotp/otp/otp_format';
    public const XML_PATH_CUSTOMER_LOGIN_OTP_LENGTH = 'customerloginotp/otp/otp_length';
    public const XML_PATH_CUSTOMER_LOGIN_OTP_RESEND = "customerloginotp/otp/otp_resend";
    public const XML_PATH_CUSTOMER_LOGIN_OTP_TEMPLATE = "customerloginotp/otp/otp_template";
    public const XML_PATH_CUSTOMER_LOGIN_OTP_API_ENDPOINT = "customerloginotp/api/endpoint";
    public const XML_PATH_CUSTOMER_LOGIN_OTP_API_KEY = "customerloginotp/api/key";
    public const XML_PATH_CUSTOMER_LOGIN_OTP_API_SENDER = "customerloginotp/api/sender";
    public const XML_PATH_CUSTOMER_LOGIN_OTP_API_VERSION = "customerloginotp/api/api_version";
    public const XML_PATH_CUSTOMER_LOGIN_OTP_CAMPAIGN_TYPE = "customerloginotp/api/campaign_type";
    public const XML_PATH_CUSTOMER_LOGIN_OTP_CONTENT_TEMPLATE_ID = "customerloginotp/api/content_template_id";
    public const XML_PATH_CUSTOMER_LOGIN_OTP_PRINCIPAL_ENTITY_ID = "customerloginotp/api/principal_entity_id";
    public const XML_PATH_CUSTOMER_LOGIN_OTP_EMAIL_SENDER_IDENTITY = 'customerloginotp/email/sender_email_identity';
    public const XML_PATH_CUSTOMER_LOGIN_OTP_EMAIL_EMAIL_TEMPLATE = 'customerloginotp/email/email_template';

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var StateInterface
     */
    protected $inlineTranslation;

    /**
     * @var Escaper
     */
    protected $escaper;

    /**
     * @var TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @var OtpCollectionFactory
     */
    protected $otpCollectionFactory;

    /**
     * @var ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @var Curl
     */
    protected $curl;

    /**
     * @var Json
     */
    protected $jsonSerializer;

    /**
     * @param Context $context
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param StateInterface $inlineTranslation
     * @param Escaper $escaper
     * @param TransportBuilder $transportBuilder
     * @param OtpCollectionFactory $otpCollectionFactory
     * @param ResourceConnection $resourceConnection
     * @param Curl $curl
     * @param Json $jsonSerializer
     */
    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        StateInterface $inlineTranslation,
        Escaper $escaper,
        TransportBuilder $transportBuilder,
        OtpCollectionFactory $otpCollectionFactory,
        ResourceConnection $resourceConnection,
        Curl $curl,
        Json $jsonSerializer
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->inlineTranslation = $inlineTranslation;
        $this->escaper = $escaper;
        $this->transportBuilder = $transportBuilder;
        $this->otpCollectionFactory = $otpCollectionFactory;
        $this->resourceConnection = $resourceConnection;
        $this->curl = $curl;
        $this->jsonSerializer = $jsonSerializer;
        parent::__construct($context);
    }

    /**
     * Get config value
     *
     * @param string $path
     * @return mixed
     */
    public function getScopeConfig($path, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get resend OTP time
     *
     * @return mixed
     */
    public function getResendOTPTimeLimit()
    {
        return $this->getScopeConfig(self::XML_PATH_CUSTOMER_LOGIN_OTP_RESEND);
    }

    /**
     * Generate OTP
     *
     * @param int $customerId
     * @param string $event
     * @param int|null $otpLength
     * @return string
     */
    public function generateOTP(
        $customerId,
        $event = CustomerLoginOtpHelper::OTP_REGISTRATION_EVENT,
        ?int $otpLength = null
    )
    {
        if ($customerId > 0) {
            $connection = $this->resourceConnection->getConnection();
            $connection->update(
                Otp::EMAIL_OTP_TABLE_NAME,
                ["expire" => '1'],
                ['customer_id = ?' => (int) $customerId, 'expire = ?' => 0, 'event = ?' => $event]
            );
        }

        $length = $otpLength ?: (int)$this->getScopeConfig(self::XML_PATH_CUSTOMER_LOGIN_OTP_LENGTH);
        $format = $this->getScopeConfig(self::XML_PATH_CUSTOMER_LOGIN_OTP_FORMAT);
        $generator = "";

        switch ($format) {
            case 1:
                $generator = "1357902468";
                break;
            case 2:
                $generator = "DZBYAFRNQVJPKWGMLSEOIHXCUT";
                break;
            case 3:
                $generator = "1357902468DZBYAFRNQVJPKWGMLSEOIHXCUT";
                break;
        }
        $otp = $this->randomOTP($generator, $length);
        return $otp;
    }

    /**
     * Update OTP fields
     *
     * @param $otp
     * @param $phoneNumber
     * @param $field
     * @return void
     */
    private function updateOtpMessage($otp, $phoneNumber, $field = [])
    {
        if ($phoneNumber && $field) {
            $connection = $this->resourceConnection->getConnection();
            $connection->update(
                Otp::EMAIL_OTP_TABLE_NAME,
                $field,
                ['phone = ?' => $phoneNumber, 'otp = ?' => $otp]
            );
        }
    }

    /**
     * Generate random OTP
     *
     * @param string $generator
     * @param int $length
     * @return string
     */
    public function randomOTP($generator, $length = 6)
    {
        $otp = "";
        for ($i = 1; $i <= $length; $i++) {
            $otp .= substr($generator, (rand()%(strlen($generator))), 1);
        }

        $collection = $this->otpCollectionFactory->create()
            ->addFieldToFilter("otp", ["eq" => $otp]);
        if ($collection->count() > 0) {
            $this->randomOTP($generator, $length);
        }
        return $otp;
    }

    /**
     * Send OTP
     *
     * @param OTP|string $otp
     * @param mixed $phoneNumber
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\MailException
     */
    public function sendOtp($otp, $phoneNumber = "", $storeId = null)
    {
        $otpCode = (string)$otp;
        $to = preg_replace('/\D+/', '', (string)$phoneNumber);

        $store = $storeId ? $this->storeManager->getStore((int)$storeId) : $this->storeManager->getStore();
        $websiteName = $store->getWebsite()->getName();

        try {
            // OTP message/template
            $template = $this->getScopeConfig(
                self::XML_PATH_CUSTOMER_LOGIN_OTP_TEMPLATE,
                $storeId
            );
            $template = $this->sanitizeTemplate(
                $template,
                $otpCode,
                $websiteName
            );
            $apiEndPoint = $this->getScopeConfig(self::XML_PATH_CUSTOMER_LOGIN_OTP_API_ENDPOINT, $storeId);
            $apiVersion = $this->getScopeConfig(self::XML_PATH_CUSTOMER_LOGIN_OTP_API_VERSION, $storeId);
            $sender = $this->getScopeConfig(self::XML_PATH_CUSTOMER_LOGIN_OTP_API_SENDER, $storeId);
            $campaignType = $this->getScopeConfig(self::XML_PATH_CUSTOMER_LOGIN_OTP_CAMPAIGN_TYPE, $storeId);
            $contentTemplateId = $this->getScopeConfig(self::XML_PATH_CUSTOMER_LOGIN_OTP_CONTENT_TEMPLATE_ID, $storeId);
            $principalEntityId = $this->getScopeConfig(self::XML_PATH_CUSTOMER_LOGIN_OTP_PRINCIPAL_ENTITY_ID, $storeId);

            $requestData = [
                'version' => $apiVersion,
                'smsData' => [
                    'toNumber' => $to,
                    'fromNumber' => $sender,
                    'body' => $template
                ],
                'metadata' => [
                    'campaignType' => $campaignType,
                    'indiaDLT' => [
                        'contentTemplateId' => $contentTemplateId,
                        'principalEntityId' => $principalEntityId
                    ]
                ]
            ];
            
            $authorizationHeader = $this->getScopeConfig(self::XML_PATH_CUSTOMER_LOGIN_OTP_API_KEY, $storeId);
            if (stripos($authorizationHeader, 'Authorization:') === 0) {
                $authorizationHeader = trim(substr($authorizationHeader, strlen('Authorization:')));
            }
            if ($authorizationHeader && stripos($authorizationHeader, 'Basic ') !== 0) {
                $authorizationHeader = 'Basic ' . $authorizationHeader;
            }

            $this->curl->setHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => $authorizationHeader
            ]);

            $this->curl->setTimeout(30);

            $this->curl->post(
                $apiEndPoint,
                $this->jsonSerializer->serialize($requestData)
            );

            $responseStatus = $this->curl->getStatus();
            $responseBody = $this->curl->getBody();

            $responseMessage = "Statuscode=" . $responseStatus
                . " & Response=" . $responseBody;
            $this->updateOtpMessage(
                $otp,
                $phoneNumber,
                [
                    'message' => $template,
                    'response_message' => $responseMessage
                ]
            );

            if ((int)$responseStatus !== 200) {
                throw new LocalizedException(
                    __('OTP SMS failed. HTTP status: %1', $responseStatus)
                );
            }

            return true;

        } catch (\Magento\Framework\Exception\MailException $e) {
            throw new \Magento\Framework\Exception\MailException(
                __($e->getMessage())
            );
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Send Email OTP
     *
     * @param OTP|string $otp
     * @param mixed $to
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\MailException
     */
    public function sendEmailOtp($otp, $customer = "")
    {
        $sendTo = [];

        $sendTo['email'] = $customer->getEmail();
        $sendTo['name'] = $customer->getName();
        $otpCode = $otp;

        $this->inlineTranslation->suspend();

        $sender = $this->getScopeConfig(self::XML_PATH_CUSTOMER_LOGIN_OTP_EMAIL_SENDER_IDENTITY);

        $senderIdentity = [
            'name' => $this->getScopeConfig("trans_email/ident_" . $sender . "/name"),
            'email' => $this->getScopeConfig("trans_email/ident_" . $sender . "/email")
        ];

        try {
            $data = [];
            $data['customer'] = $sendTo;
            $data['otp'] = $otpCode;
            $data['otp_time'] = $this->getResendOTPTimeLimit();
            $transport = $this->transportBuilder
                ->setTemplateIdentifier($this->getScopeConfig(self::XML_PATH_CUSTOMER_LOGIN_OTP_EMAIL_EMAIL_TEMPLATE))
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                        'store' => $customer->getStore()->getId(),
                    ]
                )
                ->setTemplateVars(['data' => new DataObject($data)])
                ->setFrom($senderIdentity)
                ->addTo($sendTo['email'])
                ->getTransport();
            $transport->sendMessage();
        } catch (\Magento\Framework\Exception\MailException $e) {
            throw new \Magento\Framework\Exception\MailException(__($e->getMessage()));
        } catch (Exception $e) {
            throw new \Exception($e->getMessage());
        } finally {
            $this->inlineTranslation->resume();
        }
    }

    /**
     * Replace code with value
     *
     * @param $template
     * @param $otp
     * @param $websiteName
     * @return array|string|string[]
     */
    private function sanitizeTemplate($template, $otp, $websiteName)
    {
        $placeholders = [
            '{{OTP}}',
            '{{Otp}}',
            '{{otp}}',
            '{{StoreName}}',
            '{{storename}}'
        ];

        $replacements = [
            $otp,
            $otp,
            $otp,
            $websiteName,
            $websiteName
        ];

        return str_replace($placeholders, $replacements, $template);
    }

    /**
     * Validate OTP
     *
     * @param string $otp
     * @param string $attributeValue
     * @param string $otpValidationOn
     * @param string $event
     * @param int|null $websiteId
     * @return bool
     */
    public function validateOTP($otp, $attributeValue, $otpValidationOn, $event, $websiteId = null)
    {
        $success = false;
        $event = strtolower($event);

        if ($otp != "" && $attributeValue != "") {
            $collection = $this->otpCollectionFactory->create()
                ->addFieldToFilter("main_table.otp", ["eq" => $otp])
                ->addFieldToFilter("main_table.event", ["eq" => $event])
                ->addFieldToFilter("expire", ["eq" => 0]);

            if ($otpValidationOn == "phone") {
                $collection->addFieldToFilter("main_table.phone", ["eq" => $attributeValue]);
            }
            if ($otpValidationOn == "email") {
                $collection->addFieldToFilter("main_table.email", ["eq" => $attributeValue]);
            }

            if ($websiteId) {
                $collection->addFieldToFilter("main_table.website_id", ["eq" => (int)$websiteId]);
            }

            if ($collection->count() > 0) {
                $success = true;
                $connection = $this->resourceConnection->getConnection();
                $where = [
                    'otp = ?' => $otp,
                    $otpValidationOn . ' = ?' => $attributeValue,
                    'event = ?' => $event
                ];

                if ($websiteId) {
                    $where['website_id = ?'] = (int)$websiteId;
                }

                $connection->update(
                    Otp::EMAIL_OTP_TABLE_NAME,
                    ["expire" => '1'],
                    $where
                );
            }
        }
        return $success;
    }
}
