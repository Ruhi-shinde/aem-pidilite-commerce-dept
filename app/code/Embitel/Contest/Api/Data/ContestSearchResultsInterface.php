<?php
declare(strict_types=1);

namespace Embitel\Contest\Api\Data;

interface ContestSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get Contest list.
     * @return \Embitel\Contest\Api\Data\ContestInterface[]
     */
    public function getItems();

    /**
     * Set contest_name list.
     * @param \Embitel\Contest\Api\Data\ContestInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

