<?php
declare(strict_types=1);

namespace Embitel\ContactUs\Model;

use Embitel\ContactUs\Api\Data\ContactUsInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;

class ContactUs extends AbstractModel implements ContactUsInterface
{
    protected function _construct()
    {
        $this->_init(\Embitel\ContactUs\Model\ResourceModel\ContactUs::class);
    }

    public function beforeSave()
    {
        if (trim((string)$this->getFirstName()) === '') {
            throw new LocalizedException(__('First Name is required.'));
        }

        if (trim((string)$this->getLastName()) === '') {
            throw new LocalizedException(__('Last Name is required.'));
        }

        if (trim((string)$this->getCity()) === '') {
            throw new LocalizedException(__('City is required.'));
        }

        if (trim((string)$this->getEmailId()) === '') {
            throw new LocalizedException(__('Email ID is required.'));
        }

        if (trim((string)$this->getMobileNumber()) === '') {
            throw new LocalizedException(__('Mobile Number is required.'));
        }

        if (trim((string)$this->getMessage()) === '') {
            throw new LocalizedException(__('Message is required.'));
        }

        return parent::beforeSave();
    }

    public function getEntityId()
    {
        return $this->getData(self::ENTITY_ID);
    }

    public function setEntityId($entityId)
    {
        return $this->setData(self::ENTITY_ID, $entityId);
    }

    public function getFirstName()
    {
        return $this->getData(self::FIRST_NAME);
    }

    public function setFirstName($firstName)
    {
        return $this->setData(self::FIRST_NAME, $firstName);
    }

    public function getLastName()
    {
        return $this->getData(self::LAST_NAME);
    }

    public function setLastName($lastName)
    {
        return $this->setData(self::LAST_NAME, $lastName);
    }

    public function getCity()
    {
        return $this->getData(self::CITY);
    }

    public function setCity($city)
    {
        return $this->setData(self::CITY, $city);
    }

    public function getEmailId()
    {
        return $this->getData(self::EMAIL_ID);
    }

    public function setEmailId($emailId)
    {
        return $this->setData(self::EMAIL_ID, $emailId);
    }

    public function getMobileNumber()
    {
        return $this->getData(self::MOBILE_NUMBER);
    }

    public function setMobileNumber($mobileNumber)
    {
        return $this->setData(self::MOBILE_NUMBER, $mobileNumber);
    }

    public function getMessage()
    {
        return $this->getData(self::MESSAGE);
    }

    public function setMessage($message)
    {
        return $this->setData(self::MESSAGE, $message);
    }

    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    public function getUpdatedAt()
    {
        return $this->getData(self::UPDATED_AT);
    }

    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }
}
