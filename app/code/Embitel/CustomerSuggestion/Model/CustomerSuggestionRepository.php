<?php
declare(strict_types=1);

namespace Embitel\CustomerSuggestion\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Embitel\CustomerSuggestion\Api\CustomerSuggestionRepositoryInterface;
use Embitel\CustomerSuggestion\Api\Data\CustomerSuggestionInterface;
use Embitel\CustomerSuggestion\Api\Data\CustomerSuggestionInterfaceFactory;
use Embitel\CustomerSuggestion\Api\Data\CustomerSuggestionSearchResultsInterfaceFactory;
use Embitel\CustomerSuggestion\Model\ResourceModel\CustomerSuggestion as ResourceCustomerSuggestion;
use Embitel\CustomerSuggestion\Model\ResourceModel\CustomerSuggestion\CollectionFactory;

class CustomerSuggestionRepository implements CustomerSuggestionRepositoryInterface
{
    private ResourceCustomerSuggestion $resource;
    private CustomerSuggestionInterfaceFactory $customerSuggestionFactory;
    private CollectionFactory $collectionFactory;
    private CustomerSuggestionSearchResultsInterfaceFactory $searchResultsFactory;
    private CollectionProcessorInterface $collectionProcessor;

    public function __construct(
        ResourceCustomerSuggestion $resource,
        CustomerSuggestionInterfaceFactory $customerSuggestionFactory,
        CollectionFactory $collectionFactory,
        CustomerSuggestionSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->customerSuggestionFactory = $customerSuggestionFactory;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    public function save(CustomerSuggestionInterface $customerSuggestion)
    {
        try {
            $this->resource->save($customerSuggestion);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(
                __('Could not save the customer suggestion: %1', $exception->getMessage())
            );
        }

        return $customerSuggestion;
    }

    public function get(int $entityId)
    {
        $customerSuggestion = $this->customerSuggestionFactory->create();
        $this->resource->load($customerSuggestion, $entityId);

        if (!$customerSuggestion->getId()) {
            throw new NoSuchEntityException(__('Customer suggestion with id "%1" does not exist.', $entityId));
        }

        return $customerSuggestion;
    }

    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->collectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }

    public function delete(CustomerSuggestionInterface $customerSuggestion): bool
    {
        try {
            $model = $this->customerSuggestionFactory->create();
            $this->resource->load($model, $customerSuggestion->getEntityId());
            $this->resource->delete($model);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(
                __('Could not delete the customer suggestion: %1', $exception->getMessage())
            );
        }

        return true;
    }

    public function deleteById(int $entityId): bool
    {
        return $this->delete($this->get($entityId));
    }
}
