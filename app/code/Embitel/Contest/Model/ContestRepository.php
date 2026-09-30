<?php
declare(strict_types=1);

namespace Embitel\Contest\Model;

use Embitel\Contest\Api\ContestRepositoryInterface;
use Embitel\Contest\Api\Data\ContestInterface;
use Embitel\Contest\Api\Data\ContestInterfaceFactory;
use Embitel\Contest\Api\Data\ContestSearchResultsInterfaceFactory;
use Embitel\Contest\Model\ResourceModel\Contest as ResourceContest;
use Embitel\Contest\Model\ResourceModel\Contest\CollectionFactory as ContestCollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class ContestRepository implements ContestRepositoryInterface
{

    /**
     * @var ResourceContest
     */
    protected $resource;

    /**
     * @var ContestInterfaceFactory
     */
    protected $contestFactory;

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * @var Contest
     */
    protected $searchResultsFactory;

    /**
     * @var ContestCollectionFactory
     */
    protected $contestCollectionFactory;


    /**
     * @param ResourceContest $resource
     * @param ContestInterfaceFactory $contestFactory
     * @param ContestCollectionFactory $contestCollectionFactory
     * @param ContestSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        ResourceContest $resource,
        ContestInterfaceFactory $contestFactory,
        ContestCollectionFactory $contestCollectionFactory,
        ContestSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->contestFactory = $contestFactory;
        $this->contestCollectionFactory = $contestCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritDoc
     */
    public function save(ContestInterface $contest)
    {
        try {
            $this->resource->save($contest);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the contest: %1',
                $exception->getMessage()
            ));
        }
        return $contest;
    }

    /**
     * @inheritDoc
     */
    public function get($contestId)
    {
        $contest = $this->contestFactory->create();
        $this->resource->load($contest, $contestId);
        if (!$contest->getId()) {
            throw new NoSuchEntityException(__('Contest with id "%1" does not exist.', $contestId));
        }
        return $contest;
    }

    /**
     * @inheritDoc
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->contestCollectionFactory->create();
        
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

    /**
     * @inheritDoc
     */
    public function delete(ContestInterface $contest)
    {
        try {
            $contestModel = $this->contestFactory->create();
            $this->resource->load($contestModel, $contest->getContestId());
            $this->resource->delete($contestModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Contest: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById($contestId)
    {
        return $this->delete($this->get($contestId));
    }
}

