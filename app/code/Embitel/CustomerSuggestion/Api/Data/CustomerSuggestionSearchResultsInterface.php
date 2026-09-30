<?php
declare(strict_types=1);

namespace Embitel\CustomerSuggestion\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface CustomerSuggestionSearchResultsInterface extends SearchResultsInterface
{
    /**
     * @return \Embitel\CustomerSuggestion\Api\Data\CustomerSuggestionInterface[]
     */
    public function getItems();

    /**
     * @param \Embitel\CustomerSuggestion\Api\Data\CustomerSuggestionInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
