<?php
namespace Packs\Magento2\Model\ResourceModel\Order\Grid;

use Magento\Sales\Model\ResourceModel\Order\Grid\Collection as OriginalCollection;

/**
 * Order grid extended collection
 */
class Collection extends OriginalCollection
{
    protected function _renderFiltersBefore()
    {
        $joinTable = $this->getTable('packs_magento2_shipment');
        $this->getSelect()->joinLeft($joinTable, 'main_table.entity_id = '.$joinTable.'.magento_order_id', ['confirm_date','confirm_status']);
        parent::_renderFiltersBefore();
    }
}