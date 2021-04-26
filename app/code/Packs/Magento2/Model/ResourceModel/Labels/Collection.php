<?php

namespace Packs\Magento2\Model\ResourceModel\Labels;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Packs\Magento2\Model\Labels;
use Packs\Magento2\Model\ResourceModel\Labels as LabelsResource;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'entity_id';

    protected function _construct()
    {
        $this->_init(Labels::class, LabelsResource::class);
    }
}