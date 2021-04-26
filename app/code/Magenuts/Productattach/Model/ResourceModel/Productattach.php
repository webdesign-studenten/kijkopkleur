<?php

namespace Magenuts\Productattach\Model\ResourceModel;

/**
 * Productattach Resource Model
 */
class Productattach extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('magenuts_productattach', 'productattach_id');
    }
}
