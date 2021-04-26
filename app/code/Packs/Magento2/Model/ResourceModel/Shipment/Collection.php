<?php

namespace Packs\Magento2\Model\ResourceModel\Shipment;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Packs\Magento2\Model\Shipment;
use Packs\Magento2\Model\ResourceModel\Shipment as ShipmentResource;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'entity_id';

    protected function _construct()
    {
        $this->_init(Shipment::class, ShipmentResource::class);

    }
}