<?php
declare(strict_types=1);

namespace Embitel\Bookmark\Model;

use Embitel\Bookmark\Api\Data\BookmarkInterface;
use Magento\Framework\Model\AbstractModel;

class Bookmark extends AbstractModel implements BookmarkInterface
{
    protected function _construct()
    {
        $this->_init(\Embitel\Bookmark\Model\ResourceModel\Bookmark::class);
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

    public function getBookmarkId()
    {
        return $this->getData(self::BOOKMARK_ID);
    }

    public function setBookmarkId($bookmarkId)
    {
        return $this->setData(self::BOOKMARK_ID, $bookmarkId);
    }

    public function getChildId()
    {
        return $this->getData(self::CHILD_ID);
    }

    public function setChildId($childId)
    {
        return $this->setData(self::CHILD_ID, $childId);
    }

    public function getContentType()
    {
        return $this->getData(self::CONTENT_TYPE);
    }

    public function setContentType($contentType)
    {
        return $this->setData(self::CONTENT_TYPE, $contentType);
    }

    public function getContentName()
    {
        return $this->getData(self::CONTENT_NAME);
    }

    public function setContentName($contentName)
    {
        return $this->setData(self::CONTENT_NAME, $contentName);
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
