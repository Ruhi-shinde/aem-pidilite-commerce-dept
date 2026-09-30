<?php
declare(strict_types=1);

namespace Embitel\Contest\Api\Data;

interface ContestInterface
{

    const PARENT_PHONE = 'parent_phone';
    const CHILD_NAME = 'child_name';
    const USER_ID = 'user_id';
    const ARTWORK_TITLE = 'artwork_title';
    const PARENT_EMAIL = 'parent_email';
    const DOCUMENTS = 'documents';
    const CONTEST_NAME = 'contest_name';
    const CONTEST_TITLE = 'contest_title';
    const BANNER_IMAGE = 'banner_image';
    const CTA_LINK = 'cta_link';
    const CONTEST_ID = 'contest_id';
    const DESCRIPTION = 'description';

    /**
     * Get contest_id
     * @return string|null
     */
    public function getContestId();

    /**
     * Set contest_id
     * @param string $contestId
     * @return \Embitel\Contest\Contest\Api\Data\ContestInterface
     */
    public function setContestId($contestId);

    /**
     * Get contest_name
     * @return string|null
     */
    public function getContestName();

    /**
     * Set contest_name
     * @param string $contestName
     * @return \Embitel\Contest\Contest\Api\Data\ContestInterface
     */
    public function setContestName($contestName);

    /**
     * Get contest_title
     * @return string|null
     */
    public function getContestTitle();

    /**
     * Set contest_title
     * @param string $contestTitle
     * @return \Embitel\Contest\Contest\Api\Data\ContestInterface
     */
    public function setContestTitle($contestTitle);

    /**
     * Get user_id
     * @return string|null
     */
    public function getUserId();

    /**
     * Set user_id
     * @param string $userId
     * @return \Embitel\Contest\Contest\Api\Data\ContestInterface
     */
    public function setUserId($userId);

    /**
     * Get child_name
     * @return string|null
     */
    public function getChildName();

    /**
     * Set child_name
     * @param string $childName
     * @return \Embitel\Contest\Contest\Api\Data\ContestInterface
     */
    public function setChildName($childName);

    /**
     * Get parent_email
     * @return string|null
     */
    public function getParentEmail();

    /**
     * Set parent_email
     * @param string $parentEmail
     * @return \Embitel\Contest\Contest\Api\Data\ContestInterface
     */
    public function setParentEmail($parentEmail);

    /**
     * Get parent_phone
     * @return string|null
     */
    public function getParentPhone();

    /**
     * Set parent_phone
     * @param string $parentPhone
     * @return \Embitel\Contest\Contest\Api\Data\ContestInterface
     */
    public function setParentPhone($parentPhone);

    /**
     * Get artwork_title
     * @return string|null
     */
    public function getArtworkTitle();

    /**
     * Set artwork_title
     * @param string $artworkTitle
     * @return \Embitel\Contest\Contest\Api\Data\ContestInterface
     */
    public function setArtworkTitle($artworkTitle);

    /**
     * Get banner_image
     * @return string|null
     */
    public function getBannerImage();

    /**
     * Set banner_image
     * @param string $bannerImage
     * @return \Embitel\Contest\Contest\Api\Data\ContestInterface
     */
    public function setBannerImage($bannerImage);

    /**
     * Get cta_link
     * @return string|null
     */
    public function getCtaLink();

    /**
     * Set cta_link
     * @param string $ctaLink
     * @return \Embitel\Contest\Contest\Api\Data\ContestInterface
     */
    public function setCtaLink($ctaLink);

    /**
     * Get description
     * @return string|null
     */
    public function getDescription();

    /**
     * Set description
     * @param string $description
     * @return \Embitel\Contest\Contest\Api\Data\ContestInterface
     */
    public function setDescription($description);

    /**
     * Get documents
     * @return string|null
     */
    public function getDocuments();

    /**
     * Set documents
     * @param string $documents
     * @return \Embitel\Contest\Contest\Api\Data\ContestInterface
     */
    public function setDocuments($documents);
}

