<?php
declare(strict_types=1);

namespace Embitel\ContactUs\Api;

use Embitel\ContactUs\Api\Data\ContactUsInterface;
use Embitel\ContactUs\Api\Data\ContactUsSearchResultsInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

interface ContactUsRepositoryInterface
{
    /**
     * @param ContactUsInterface $contactUs
     * @return ContactUsInterface
     */
    public function save(ContactUsInterface $contactUs);

    /**
     * @param int $entityId
     * @return ContactUsInterface
     */
    public function get(int $entityId);

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return ContactUsSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * @param ContactUsInterface $contactUs
     * @return bool
     */
    public function delete(ContactUsInterface $contactUs): bool;

    /**
     * @param int $entityId
     * @return bool
     */
    public function deleteById(int $entityId): bool;
}
