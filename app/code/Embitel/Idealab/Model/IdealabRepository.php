<?php
declare(strict_types=1);

namespace Embitel\Idealab\Model;

use Embitel\Idealab\Api\Data\IdealabInterface;
use Embitel\Idealab\Api\Data\IdealabInterfaceFactory;
use Embitel\Idealab\Api\Data\IdealabSearchResultsInterfaceFactory;
use Embitel\Idealab\Api\IdealabRepositoryInterface;
use Embitel\Idealab\Model\ResourceModel\Idealab as ResourceIdealab;
use Embitel\Idealab\Model\ResourceModel\Idealab\CollectionFactory as IdealabCollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class IdealabRepository implements IdealabRepositoryInterface
{
    protected $resource;
    protected $idealabFactory;
    protected $collectionProcessor;
    protected $searchResultsFactory;
    protected $idealabCollectionFactory;

    public function __construct(
        ResourceIdealab $resource,
        IdealabInterfaceFactory $idealabFactory,
        IdealabCollectionFactory $idealabCollectionFactory,
        IdealabSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->idealabFactory = $idealabFactory;
        $this->idealabCollectionFactory = $idealabCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    public function save(IdealabInterface $idealab)
    {
        try {
            $this->resource->save($idealab);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the idealab: %1',
                $exception->getMessage()
            ));
        }
        return $idealab;
    }

    public function get($idealabId)
    {
        $idealab = $this->idealabFactory->create();
        $this->resource->load($idealab, $idealabId);
        if (!$idealab->getId()) {
            throw new NoSuchEntityException(__('Idealab with id "%1" does not exist.', $idealabId));
        }
        return $idealab;
    }

    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $criteria)
    {
        $collection = $this->idealabCollectionFactory->create();

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

    public function delete(IdealabInterface $idealab)
    {
        try {
            $idealabModel = $this->idealabFactory->create();
            $this->resource->load($idealabModel, $idealab->getIdealabId());
            $this->resource->delete($idealabModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Idealab: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    public function deleteById($idealabId)
    {
        return $this->delete($this->get($idealabId));
    }
}
