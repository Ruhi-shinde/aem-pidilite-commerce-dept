<?php
declare(strict_types=1);

namespace Embitel\Bookmark\Api\Data;

interface BookmarkSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get Bookmark list.
     * @return \Embitel\Bookmark\Api\Data\BookmarkInterface[]
     */
    public function getItems();

    /**
     * Set Bookmark list.
     * @param \Embitel\Bookmark\Api\Data\BookmarkInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
