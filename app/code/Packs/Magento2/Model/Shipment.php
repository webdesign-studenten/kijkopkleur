<?php

namespace Packs\Magento2\Model;

use Magento\Framework\Model\AbstractModel;

class Shipment extends AbstractModel
{
    protected $_eventPrefix = 'packs_magento2_shipment';

    protected function _construct()
    {
        $this->_init(\Packs\Magento2\Model\ResourceModel\Shipment::class);
    }
}