<?php

/**
 * Productattach Resource Collection
 */
namespace Magenuts\Productattach\Model\ResourceModel\Productattach;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    protected $_idFieldName = 'productattach_id';

    /**
     * Resource initialization
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(
            'Magenuts\Productattach\Model\Productattach',
            'Magenuts\Productattach\Model\ResourceModel\Productattach'
        );
    }
}
