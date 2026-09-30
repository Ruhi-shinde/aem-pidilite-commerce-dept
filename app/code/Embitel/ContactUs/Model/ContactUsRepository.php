<?php
declare(strict_types=1);

namespace Embitel\ContactUs\Model;

use Embitel\ContactUs\Api\ContactUsRepositoryInterface;
use Embitel\ContactUs\Api\Data\ContactUsInterface;
use Embitel\ContactUs\Api\Data\ContactUsInterfaceFactory;
use Embitel\ContactUs\Api\Data\ContactUsSearchResultsInterfaceFactory;
use Embitel\ContactUs\Model\ResourceModel\ContactUs as ResourceContactUs;
use Embitel\ContactUs\Model\ResourceModel\ContactUs\CollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class ContactUsRepository implements ContactUsRepositoryInterface
{
    private ResourceContactUs $resource;
    private ContactUsInterfaceFactory $contactUsFactory;
    private CollectionFactory $collectionFactory;
    private ContactUsSearchResultsInterfaceFactory $searchResultsFactory;
    private CollectionProcessorInterface $collectionProcessor;

    public function __construct(
        ResourceContactUs $resource,
        ContactUsInterfaceFactory $contactUsFactory,
        CollectionFactory $collectionFactory,
        ContactUsSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->contactUsFactory = $contactUsFactory;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    public function save(ContactUsInterface $contactUs)
    {
        try {
            $this->resource->save($contactUs);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(
                __('Could not save contact us record: %1', $exception->getMessage())
            );
        }

        return $contactUs;
    }

    public function get(int $entityId)
    {
        $contactUs = $this->contactUsFactory->create();
        $this->resource->load($contactUs, $entityId);

        if (!$contactUs->getId()) {
            throw new NoSuchEntityException(__('Contact us record with id "%1" does not exist.', $entityId));
        }

        return $contactUs;
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

    public function delete(ContactUsInterface $contactUs): bool
    {
        try {
            $model = $this->contactUsFactory->create();
            $this->resource->load($model, $contactUs->getEntityId());
            $this->resource->delete($model);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(
                __('Could not delete contact us record: %1', $exception->getMessage())
            );
        }

        return true;
    }

    public function deleteById(int $entityId): bool
    {
        return $this->delete($this->get($entityId));
    }
}
