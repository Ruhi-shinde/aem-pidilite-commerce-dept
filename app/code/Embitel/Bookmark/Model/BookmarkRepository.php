<?php
declare(strict_types=1);

namespace Embitel\Bookmark\Model;

use Embitel\Bookmark\Api\BookmarkRepositoryInterface;
use Embitel\Bookmark\Api\Data\BookmarkInterface;
use Embitel\Bookmark\Api\Data\BookmarkInterfaceFactory;
use Embitel\Bookmark\Api\Data\BookmarkSearchResultsInterfaceFactory;
use Embitel\Bookmark\Model\ResourceModel\Bookmark as ResourceBookmark;
use Embitel\Bookmark\Model\ResourceModel\Bookmark\CollectionFactory as BookmarkCollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class BookmarkRepository implements BookmarkRepositoryInterface
{
    protected $resource;
    protected $bookmarkFactory;
    protected $collectionProcessor;
    protected $searchResultsFactory;
    protected $bookmarkCollectionFactory;

    public function __construct(
        ResourceBookmark $resource,
        BookmarkInterfaceFactory $bookmarkFactory,
        BookmarkCollectionFactory $bookmarkCollectionFactory,
        BookmarkSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->bookmarkFactory = $bookmarkFactory;
        $this->bookmarkCollectionFactory = $bookmarkCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    public function save(BookmarkInterface $bookmark)
    {
        try {
            $this->resource->save($bookmark);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the bookmark: %1',
                $exception->getMessage()
            ));
        }
        return $bookmark;
    }

    public function get($entityId)
    {
        $bookmark = $this->bookmarkFactory->create();
        $this->resource->load($bookmark, $entityId);
        if (!$bookmark->getId()) {
            throw new NoSuchEntityException(__('Bookmark with id "%1" does not exist.', $entityId));
        }
        return $bookmark;
    }

    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $criteria)
    {
        $collection = $this->bookmarkCollectionFactory->create();

        $this->collectionProcessor->process($criteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $items = [];
        foreach ($collection as $model) {
            $items[] = $model;
        }

        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    public function delete(BookmarkInterface $bookmark)
    {
        try {
            $bookmarkModel = $this->bookmarkFactory->create();
            $this->resource->load($bookmarkModel, $bookmark->getEntityId());
            $this->resource->delete($bookmarkModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Bookmark: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    public function deleteById($entityId)
    {
        return $this->delete($this->get($entityId));
    }
}
