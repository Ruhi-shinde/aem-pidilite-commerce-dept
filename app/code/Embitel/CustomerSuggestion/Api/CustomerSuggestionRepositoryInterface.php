<?php
declare(strict_types=1);

namespace Embitel\CustomerSuggestion\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Embitel\CustomerSuggestion\Api\Data\CustomerSuggestionInterface;
use Embitel\CustomerSuggestion\Api\Data\CustomerSuggestionSearchResultsInterface;

interface CustomerSuggestionRepositoryInterface
{
    /**
     * @param CustomerSuggestionInterface $customerSuggestion
     * @return CustomerSuggestionInterface
     */
    public function save(CustomerSuggestionInterface $customerSuggestion);

    /**
     * @param int $entityId
     * @return CustomerSuggestionInterface
     */
    public function get(int $entityId);

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return CustomerSuggestionSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * @param CustomerSuggestionInterface $customerSuggestion
     * @return bool
     */
    public function delete(CustomerSuggestionInterface $customerSuggestion): bool;

    /**
     * @param int $entityId
     * @return bool
     */
    public function deleteById(int $entityId): bool;
}
