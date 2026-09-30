<?php
declare(strict_types=1);

namespace Embitel\ContactUs\Model\ContactUs;

use Embitel\ContactUs\Model\ResourceModel\ContactUs\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;

class DataProvider extends AbstractDataProvider
{
    private DataPersistorInterface $dataPersistor;

    private array $loadedData = [];

    public function __construct(
        string $name,
        string $primaryFieldName,
        string $requestFieldName,
        CollectionFactory $collectionFactory,
        DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    public function getData(): array
    {
        if (!empty($this->loadedData)) {
            return $this->loadedData;
        }

        foreach ($this->collection->getItems() as $model) {
            $this->loadedData[(int)$model->getId()] = $model->getData();
        }

        $persistedData = $this->dataPersistor->get('embitel_contact_us');
        if (!empty($persistedData)) {
            $model = $this->collection->getNewEmptyItem();
            $model->setData($persistedData);
            $this->loadedData[(int)$model->getId()] = $model->getData();
            $this->dataPersistor->clear('embitel_contact_us');
        }

        return $this->loadedData;
    }
}
