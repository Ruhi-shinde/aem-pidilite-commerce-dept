<?php

namespace Fevicreate\CustomerLoginOtp\Model\ResourceModel\Otp;

use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactory;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Otp Resource Collection
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param EntityFactoryInterface $entityFactory
     * @param LoggerInterface $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface $eventManager
     * @param StoreManagerInterface $storeManager
     * @param AdapterInterface|null $connection
     * @param AbstractDb|null $resource
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        StoreManagerInterface $storeManager,
        ?AdapterInterface $connection = null,
        ?AbstractDb $resource = null
    ) {
        $this->storeManager = $storeManager;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Fevicreate\CustomerLoginOtp\Model\Otp::class,
            \Fevicreate\CustomerLoginOtp\Model\ResourceModel\Otp::class
        );
        $this->_map['fields']['otp_id'] = 'main_table.otp_id';
    }

    /**
     * Join table in a select query
     *
     * @return $this|Collection|void
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->getSelect()->join(
            ['ce' => $this->getConnection()->getTableName('customer_entity')],
            'main_table.customer_id = ce.entity_id',
            [
                'customer_name' => 'CONCAT(`ce`.`firstname`, " ", `ce`.`lastname`)'
            ]
        );
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field === 'customer_name') {
            $customerTable = $this->getConnection()->getTableName('customer_entity');
            $this->getSelect()->joinLeft(
                ['cust' => $customerTable],
                'main_table.customer_id = cust.entity_id',
                []
            );
            $conditionSql = $this->_getConditionSql(
                'CONCAT(`cust`.`firstname`, " ", `cust`.`lastname`)',
                $condition
            );
            $this->getSelect()->where($conditionSql);
            return $this;
        }

        return parent::addFieldToFilter($field, $condition);
    }
}
