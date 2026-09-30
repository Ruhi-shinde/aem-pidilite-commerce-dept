<?php
declare(strict_types=1);

namespace Embitel\Idealab\Api;

interface IdealabRepositoryInterface
{

    /**
     * Save Idealab
     * @param \Embitel\Idealab\Api\Data\IdealabInterface $idealab
     * @return \Embitel\Idealab\Api\Data\IdealabInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Embitel\Idealab\Api\Data\IdealabInterface $idealab
    );

    /**
     * Retrieve Idealab
     * @param string $idealabId
     * @return \Embitel\Idealab\Api\Data\IdealabInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($idealabId);

    /**
     * Retrieve Idealab matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Embitel\Idealab\Api\Data\IdealabSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Idealab
     * @param \Embitel\Idealab\Api\Data\IdealabInterface $idealab
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Embitel\Idealab\Api\Data\IdealabInterface $idealab
    );

    /**
     * Delete Idealab by ID
     * @param string $idealabId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($idealabId);
}
