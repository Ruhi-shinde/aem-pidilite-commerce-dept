<?php
declare(strict_types=1);

namespace Embitel\Idealab\Model\ResourceModel\Idealab\Grid;

use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;

class Collection extends SearchResult
{
    protected function _initSelect()
    {
        parent::_initSelect();

        $customerTable = $this->getTable('customer_entity');
        $nameExpression = new \Zend_Db_Expr(
            "COALESCE(NULLIF(TRIM(CONCAT(IFNULL(ce.firstname, ''), ' ', IFNULL(ce.lastname, ''))), ''), ce.email)"
        );

        $this->getSelect()->joinLeft(
            ['ce' => $customerTable],
            'main_table.user_id = ce.entity_id',
            [
                'user_email' => 'email',
                'user_name' => $nameExpression
            ]
        );

        $this->addFilterToMap('idealab_id', 'main_table.idealab_id');
        $this->addFilterToMap('user_id', 'main_table.user_id');
        $this->addFilterToMap('user_email', 'ce.email');
        $this->addFilterToMap('user_name', $nameExpression);
        $this->addFilterToMap('created_at', 'main_table.created_at');

        return $this;
    }
}
