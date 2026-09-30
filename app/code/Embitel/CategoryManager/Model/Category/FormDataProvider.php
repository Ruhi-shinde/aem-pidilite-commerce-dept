<?php

namespace Embitel\CategoryManager\Model\Category;

use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;

/**
 * FormDataProvider class provides data for the category edit form in the admin panel.
 */
class FormDataProvider extends AbstractDataProvider
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\Collection
     */
    protected $collection;

    /**
     * @var RequestInterface
     */
    private RequestInterface $request;

    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @var array
     */
    private array $loadedData = [];

    /**
     * FormDataProvider constructor.
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $categoryCollectionFactory
     * @param RequestInterface $request
     * @param StoreManagerInterface $storeManager
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $categoryCollectionFactory,
        RequestInterface $request,
        StoreManagerInterface $storeManager,
        array $meta = [],
        array $data = []
    ) {

        $this->collection = $categoryCollectionFactory->create();
        $storeId = 0;
        $this->collection->setStoreId($storeId);

        $this->collection->addAttributeToSelect([
            'name',
            'url_key',
            'description',
            'image',
            'category_banner',
            'is_active',
            'parent_id',
            'is_standard'
        ]);

        $this->request = $request;
        $this->storeManager = $storeManager;

        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $meta,
            $data
        );
    }

    /**
     * Get data for the category edit form.
     *
     * @return array
     */
    public function getData()
    {
        if (!empty($this->loadedData)) {
            return $this->loadedData;
        }

        $id = (int)$this->request->getParam('id');

        if ($id) {

            $category = $this->collection
            ->addFieldToFilter('entity_id', $id)
            ->getFirstItem();

            if ($category->getId()) {

                $this->loadedData[$category->getId()] = [
                'entity_id' => (string)$category->getId(),
                'name' => (string)$category->getName(),
                'url_key' => (string)$category->getUrlKey(),
                'description' => (string)$category->getDescription(),
                'parent_id' => (string)$category->getParentId(),
                'is_active' => (string)$category->getIsActive(),
                'is_standard' => (string)$category->getIsStandard(),
                'image' => $this->getImageData($category->getImage()),
                'category_banner' => $this->getImageData($category->getData('category_banner'))
                ];
            }
        }

        return $this->loadedData;
    }

    /**
     * Get image data for the category image attribute.
     *
     * @param string|null $image
     * @return array
     */
    private function getImageData(?string $image): array
    {
        if (!$image) {
            return [];
        }

        return [[
            'name' => $image,
            'url' => $this->storeManager->getStore()->getBaseUrl(
                \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
            ) . 'catalog/category/' . ltrim($image, '/')
        ]];
    }
}
