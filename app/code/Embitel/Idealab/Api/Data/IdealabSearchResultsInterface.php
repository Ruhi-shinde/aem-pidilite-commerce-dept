<?php
declare(strict_types=1);

namespace Embitel\Idealab\Api\Data;

interface IdealabSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get Idealab list.
     * @return \Embitel\Idealab\Api\Data\IdealabInterface[]
     */
    public function getItems();

    /**
     * Set user_id list.
     * @param \Embitel\Idealab\Api\Data\IdealabInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
