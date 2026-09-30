<?php
declare(strict_types=1);

namespace Embitel\Contest\Model;

use Embitel\Contest\Api\Data\ContestInterface;
use Magento\Framework\Model\AbstractModel;

class Contest extends AbstractModel implements ContestInterface
{

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(\Embitel\Contest\Model\ResourceModel\Contest::class);
    }

    /**
     * @inheritDoc
     */
    public function getContestId()
    {
        return $this->getData(self::CONTEST_ID);
    }

    /**
     * @inheritDoc
     */
    public function setContestId($contestId)
    {
        return $this->setData(self::CONTEST_ID, $contestId);
    }

    /**
     * @inheritDoc
     */
    public function getContestName()
    {
        return $this->getData(self::CONTEST_NAME);
    }

    /**
     * @inheritDoc
     */
    public function setContestName($contestName)
    {
        return $this->setData(self::CONTEST_NAME, $contestName);
    }

    /**
     * @inheritDoc
     */
    public function getContestTitle()
    {
        return $this->getData(self::CONTEST_TITLE);
    }

    /**
     * @inheritDoc
     */
    public function setContestTitle($contestTitle)
    {
        return $this->setData(self::CONTEST_TITLE, $contestTitle);
    }

    /**
     * @inheritDoc
     */
    public function getUserId()
    {
        return $this->getData(self::USER_ID);
    }

    /**
     * @inheritDoc
     */
    public function setUserId($userId)
    {
        return $this->setData(self::USER_ID, $userId);
    }

    /**
     * @inheritDoc
     */
    public function getChildName()
    {
        return $this->getData(self::CHILD_NAME);
    }

    /**
     * @inheritDoc
     */
    public function setChildName($childName)
    {
        return $this->setData(self::CHILD_NAME, $childName);
    }

    /**
     * @inheritDoc
     */
    public function getParentEmail()
    {
        return $this->getData(self::PARENT_EMAIL);
    }

    /**
     * @inheritDoc
     */
    public function setParentEmail($parentEmail)
    {
        return $this->setData(self::PARENT_EMAIL, $parentEmail);
    }

    /**
     * @inheritDoc
     */
    public function getParentPhone()
    {
        return $this->getData(self::PARENT_PHONE);
    }

    /**
     * @inheritDoc
     */
    public function setParentPhone($parentPhone)
    {
        return $this->setData(self::PARENT_PHONE, $parentPhone);
    }

    /**
     * @inheritDoc
     */
    public function getArtworkTitle()
    {
        return $this->getData(self::ARTWORK_TITLE);
    }

    /**
     * @inheritDoc
     */
    public function setArtworkTitle($artworkTitle)
    {
        return $this->setData(self::ARTWORK_TITLE, $artworkTitle);
    }

    /**
     * @inheritDoc
     */
    public function getBannerImage()
    {
        return $this->getData(self::BANNER_IMAGE);
    }

    /**
     * @inheritDoc
     */
    public function setBannerImage($bannerImage)
    {
        return $this->setData(self::BANNER_IMAGE, $bannerImage);
    }

    /**
     * @inheritDoc
     */
    public function getCtaLink()
    {
        return $this->getData(self::CTA_LINK);
    }

    /**
     * @inheritDoc
     */
    public function setCtaLink($ctaLink)
    {
        return $this->setData(self::CTA_LINK, $ctaLink);
    }

    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        return $this->getData(self::DESCRIPTION);
    }

    /**
     * @inheritDoc
     */
    public function setDescription($description)
    {
        return $this->setData(self::DESCRIPTION, $description);
    }

    /**
     * @inheritDoc
     */
    public function getDocuments()
    {
        return $this->getData(self::DOCUMENTS);
    }

    /**
     * @inheritDoc
     */
    public function setDocuments($documents)
    {
        return $this->setData(self::DOCUMENTS, $documents);
    }

}

