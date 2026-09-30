<?php
declare(strict_types=1);

namespace Embitel\Contest\Model\Contest;

use Embitel\Contest\Model\ResourceModel\Contest\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;

class DataProvider extends AbstractDataProvider
{

    /**
     * @var array
     */
    protected $loadedData;
    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @inheritDoc
     */
    protected $collection;


    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param StoreManagerInterface $storeManager
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        DataPersistorInterface $dataPersistor,
        StoreManagerInterface $storeManager,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        $this->storeManager = $storeManager;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * @inheritDoc
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        foreach ($items as $model) {
            $this->loadedData[$model->getId()] = $this->prepareDocumentImages($model->getData());
        }
        $data = $this->dataPersistor->get('embitel_contest_contest');
        
        if (!empty($data)) {
            $model = $this->collection->getNewEmptyItem();
            $model->setData($data);
            $this->loadedData[$model->getId()] = $this->prepareDocumentImages($model->getData());
            $this->dataPersistor->clear('embitel_contest_contest');
        }
        
        return $this->loadedData;
    }

    /**
     * Prepare documents field for admin image uploader preview.
     *
     * @param array $itemData
     * @return array
     */
    private function prepareDocumentImages(array $itemData): array
    {
        if (empty($itemData['documents'])) {
            return $itemData;
        }

        $rawDocuments = $itemData['documents'];
        $documents = [];

        if (is_string($rawDocuments)) {
            $decoded = json_decode($rawDocuments, true);
            if (is_array($decoded)) {
                $documents = $decoded;
            } else {
                $documents = [$rawDocuments];
            }
        } elseif (is_array($rawDocuments)) {
            $documents = $rawDocuments;
        }

        $mediaBaseUrl = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $preparedImages = [];

        foreach ($documents as $document) {
            if (!is_string($document) || $document === '') {
                continue;
            }

            $extension = strtolower((string)pathinfo($document, PATHINFO_EXTENSION));
            if (!in_array($extension, $imageExtensions, true)) {
                continue;
            }

            $preparedImages[] = [
                'name' => (string)basename($document),
                'url' => str_starts_with($document, 'http')
                    ? $document
                    : $mediaBaseUrl . ltrim($document, '/'),
            ];
        }

        $itemData['documents'] = $preparedImages;
        return $itemData;
    }
}

