<?php
declare(strict_types=1);

namespace Embitel\Bookmark\Api;

interface BookmarkRepositoryInterface
{
    /**
     * Save Bookmark
     * @param \Embitel\Bookmark\Api\Data\BookmarkInterface $bookmark
     * @return \Embitel\Bookmark\Api\Data\BookmarkInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Embitel\Bookmark\Api\Data\BookmarkInterface $bookmark
    );

    /**
     * Retrieve Bookmark
     * @param string $entityId
     * @return \Embitel\Bookmark\Api\Data\BookmarkInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($entityId);

    /**
     * Retrieve Bookmark matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Embitel\Bookmark\Api\Data\BookmarkSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Bookmark
     * @param \Embitel\Bookmark\Api\Data\BookmarkInterface $bookmark
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Embitel\Bookmark\Api\Data\BookmarkInterface $bookmark
    );

    /**
     * Delete Bookmark by ID
     * @param string $entityId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($entityId);
}
