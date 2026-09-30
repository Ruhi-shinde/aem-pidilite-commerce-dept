<?php
declare(strict_types=1);

namespace Embitel\ContactUs\Model\ResourceModel\ContactUs\Grid;

use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;

class Collection extends SearchResult
{
    protected function _initSelect()
    {
        parent::_initSelect();

        $this->addFilterToMap('entity_id', 'main_table.entity_id');
        $this->addFilterToMap('first_name', 'main_table.first_name');
        $this->addFilterToMap('last_name', 'main_table.last_name');
        $this->addFilterToMap('city', 'main_table.city');
        $this->addFilterToMap('mobile_number', 'main_table.mobile_number');
        $this->addFilterToMap('created_at', 'main_table.created_at');
        $this->addFilterToMap('updated_at', 'main_table.updated_at');
    }
}
