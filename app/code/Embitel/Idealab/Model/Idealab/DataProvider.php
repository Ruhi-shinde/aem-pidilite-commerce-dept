<?php
declare(strict_types=1);

namespace Embitel\Idealab\Model\Idealab;

use Embitel\Idealab\Model\ResourceModel\Idealab\CollectionFactory;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as CustomerCollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;

class DataProvider extends AbstractDataProvider
{
    protected $loadedData;
    protected $dataPersistor;
    protected $collection;
    protected $customerCollectionFactory;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        CustomerCollectionFactory $customerCollectionFactory,
        DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        $this->customerCollectionFactory = $customerCollectionFactory;
        $this->dataPersistor = $dataPersistor;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }

        $items = $this->collection->getItems();
        $userIds = [];

        foreach ($items as $item) {
            $userId = (int)$item->getData('user_id');
            if ($userId > 0) {
                $userIds[] = $userId;
            }
        }

        $customerData = [];
        if (!empty($userIds)) {
            $customerCollection = $this->customerCollectionFactory->create();
            $customerCollection->addAttributeToSelect(['firstname', 'lastname', 'email']);
            $customerCollection->addFieldToFilter('entity_id', ['in' => array_unique($userIds)]);

            foreach ($customerCollection as $customer) {
                $customerId = (int)$customer->getId();
                $fullName = trim((string)$customer->getFirstname() . ' ' . (string)$customer->getLastname());

                $customerData[$customerId] = [
                    'user_name' => $fullName,
                    'user_email' => (string)$customer->getEmail()
                ];
            }
        }

        foreach ($items as $model) {
            $data = $model->getData();
            $userId = (int)($data['user_id'] ?? 0);

            $data['user_name'] = $customerData[$userId]['user_name'] ?? '';
            $data['user_email'] = $customerData[$userId]['user_email'] ?? '';

            if (!empty($data['craft_image'])) {
                $decoded = json_decode((string)$data['craft_image'], true);
                if (is_array($decoded)) {
                    $data['craft_image'] = $decoded;
                }
            }
            $this->loadedData[$model->getId()] = $data;
        }

        $data = $this->dataPersistor->get('embitel_idealab_idealab');
        if (!empty($data)) {
            $model = $this->collection->getNewEmptyItem();
            $model->setData($data);
            $this->loadedData[$model->getId()] = $model->getData();
            $this->dataPersistor->clear('embitel_idealab_idealab');
        }

        return $this->loadedData;
    }
}
