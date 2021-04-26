<?php

namespace Packs\Magento2\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Packs\Magento2\Model\ShipmentFactory;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Data\Form\FormKey;


class Shipment extends Template {

    protected $_shipmentModelFactory;
    protected $_resource;
    protected $formKey;

    public function __construct(
        Context $context,
        ShipmentFactory $shipmentModelFactory,
        ResourceConnection $Resource,
        FormKey $formKey,
        array $data =[]
    ) {
        $this->_shipmentModelFactory = $shipmentModelFactory;
        $this->_resource = $Resource;
        $this->formKey = $formKey;

        parent::__construct($context, $data);
    }

    public function getFormKey()
    {
        return $this->formKey->getFormKey();
    }

    protected function _prepareLayout()
    {
        $text = $this->getJoinData();
        $this->setText($text);

    }

    public function getJoinData(){
        $collection = $this->_shipmentModelFactory->create()->getCollection();
        $sales_order = $this->_resource->getTableName('sales_order');

        $collection->getSelect()->joinRight(array('order' => $sales_order),
            'main_table.magento_order_id = order.increment_id');


        foreach ($collection as $item){
            print_r($item->getData());
        }
        exit();
    }

}