<?php
declare(strict_types=1);

namespace Embitel\Contest\Model\ResourceModel\Contest\Grid;

use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;

class Collection extends SearchResult
{
    protected function _initSelect()
    {
        parent::_initSelect();

        $this->getSelect()->joinLeft(
            ['customer_entity' => $this->getTable('customer_entity')],
            'main_table.user_id = customer_entity.entity_id',
            [
                'user_email' => 'email',
                'user_name' => new \Zend_Db_Expr(
                    "COALESCE(NULLIF(TRIM(CONCAT(IFNULL(customer_entity.firstname, ''), ' ', IFNULL(customer_entity.lastname, ''))), ''), customer_entity.email)"
                )
            ]
        );

        $this->addFilterToMap('contest_id', 'main_table.contest_id');
        $this->addFilterToMap('user_id', 'main_table.user_id');
        $this->addFilterToMap('user_name', new \Zend_Db_Expr(
            "COALESCE(NULLIF(TRIM(CONCAT(IFNULL(customer_entity.firstname, ''), ' ', IFNULL(customer_entity.lastname, ''))), ''), customer_entity.email)"
        ));
        $this->addFilterToMap('user_email', 'customer_entity.email');
        $this->addFilterToMap('created_at', 'main_table.created_at');
        $this->addFilterToMap('updated_at', 'main_table.updated_at');
    }
}
