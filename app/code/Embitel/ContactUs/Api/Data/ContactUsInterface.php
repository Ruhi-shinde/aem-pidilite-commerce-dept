<?php
declare(strict_types=1);

namespace Embitel\ContactUs\Api\Data;

interface ContactUsInterface
{
    public const ENTITY_ID = 'entity_id';
    public const FIRST_NAME = 'first_name';
    public const LAST_NAME = 'last_name';
    public const CITY = 'city';
    public const EMAIL_ID = 'email_id';
    public const MOBILE_NUMBER = 'mobile_number';
    public const MESSAGE = 'message';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    public function getEntityId();

    public function setEntityId($entityId);

    public function getFirstName();

    public function setFirstName($firstName);

    public function getLastName();

    public function setLastName($lastName);

    public function getCity();

    public function setCity($city);

    public function getEmailId();

    public function setEmailId($emailId);

    public function getMobileNumber();

    public function setMobileNumber($mobileNumber);

    public function getMessage();

    public function setMessage($message);

    public function getCreatedAt();

    public function setCreatedAt($createdAt);

    public function getUpdatedAt();

    public function setUpdatedAt($updatedAt);
}
