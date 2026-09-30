<?php
declare(strict_types=1);

namespace Embitel\CustomerSuggestion\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class CustomerSuggestion extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('customer_suggestion', 'entity_id');
    }
}
