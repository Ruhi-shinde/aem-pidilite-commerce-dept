<?php
declare(strict_types=1);

namespace Embitel\ContactUs\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface ContactUsSearchResultsInterface extends SearchResultsInterface
{
    /**
     * @return \Embitel\ContactUs\Api\Data\ContactUsInterface[]
     */
    public function getItems();

    /**
     * @param \Embitel\ContactUs\Api\Data\ContactUsInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
