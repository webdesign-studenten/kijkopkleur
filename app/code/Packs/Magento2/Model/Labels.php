<?php

namespace Packs\Magento2\Model;

use Magento\Framework\Model\AbstractModel;

class Labels extends AbstractModel
{
    protected $_eventPrefix = 'packs_magento2_labels';

    protected function _construct()
    {
        $this->_init(\Packs\Magento2\Model\ResourceModel\Labels::class);
    }
}
