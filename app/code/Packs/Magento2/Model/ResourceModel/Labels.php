<?php

namespace Packs\Magento2\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Labels extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('packs_magento2_labels', 'entity_id');
    }
}