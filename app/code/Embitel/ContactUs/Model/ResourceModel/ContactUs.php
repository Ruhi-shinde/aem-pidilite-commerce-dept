<?php
declare(strict_types=1);

namespace Embitel\ContactUs\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class ContactUs extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('embitel_contact_us', 'entity_id');
    }
}
