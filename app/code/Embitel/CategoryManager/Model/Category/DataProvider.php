<?php
namespace Embitel\CategoryManager\Model\Category;

use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Ui\DataProvider\AbstractDataProvider;

/**
 * DataProvider class provides data for the category grid in the admin panel.
 */
class DataProvider extends AbstractDataProvider
{
    /**
     * Inject CollectionFactory for category collection.
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        $this->collection->addAttributeToSelect(
            ['name', 'url_key', 'is_active', 'created_at', 'updated_at', 'is_standard']
        );
        $this->collection->setOrder('entity_id', 'DESC');
    }

    /**
     * Get data for grid
     *
     * @return array
     */
    public function getData()
    {
        if (!$this->getCollection()->isLoaded()) {
            $this->getCollection()->load();
        }

        $items = [];
        foreach ($this->getCollection()->getItems() as $category) {
            $categoryData = $category->getData();
            $categoryData['entity_id'] = (int) $category->getId();
            $items[] = $categoryData;
        }

        return [
            'totalRecords' => $this->getCollection()->getSize(),
            'items' => $items,
        ];
    }
}
