<?php
declare(strict_types=1);

namespace Embitel\Contest\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface ContestRepositoryInterface
{

    /**
     * Save Contest
     * @param \Embitel\Contest\Api\Data\ContestInterface $contest
     * @return \Embitel\Contest\Api\Data\ContestInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Embitel\Contest\Api\Data\ContestInterface $contest
    );

    /**
     * Retrieve Contest
     * @param string $contestId
     * @return \Embitel\Contest\Api\Data\ContestInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($contestId);

    /**
     * Retrieve Contest matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Embitel\Contest\Api\Data\ContestSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Contest
     * @param \Embitel\Contest\Api\Data\ContestInterface $contest
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Embitel\Contest\Api\Data\ContestInterface $contest
    );

    /**
     * Delete Contest by ID
     * @param string $contestId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($contestId);
}

