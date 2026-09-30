<?php
declare(strict_types=1);

namespace Embitel\CustomerSuggestion\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Embitel\CustomerSuggestion\Api\Data\CustomerSuggestionInterface;

class CustomerSuggestion extends AbstractModel implements CustomerSuggestionInterface
{
    protected function _construct()
    {
        $this->_init(\Embitel\CustomerSuggestion\Model\ResourceModel\CustomerSuggestion::class);
    }

    public function beforeSave()
    {
        if ((int)$this->getUserId() <= 0) {
            throw new LocalizedException(__('User ID is required.'));
        }

        if (trim((string)$this->getSuggestion()) === '') {
            throw new LocalizedException(__('Suggestion is required.'));
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

    public function getUserId()
    {
        return $this->getData(self::USER_ID);
    }

    public function setUserId($userId)
    {
        return $this->setData(self::USER_ID, $userId);
    }

    public function getUserName()
    {
        return $this->getData(self::USER_NAME);
    }

    public function setUserName($userName)
    {
        return $this->setData(self::USER_NAME, $userName);
    }

    public function getSuggestion()
    {
        return $this->getData(self::SUGGESTION);
    }

    public function setSuggestion($suggestion)
    {
        return $this->setData(self::SUGGESTION, $suggestion);
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
