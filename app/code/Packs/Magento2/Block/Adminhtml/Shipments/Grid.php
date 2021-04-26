<?php
namespace Packs\Magento2\Block\Adminhtml\Shipments;

use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Packs\Magento2\Model\ShipmentFactory;
use Magento\Framework\App\ResourceConnection;
use Magento\Sales\Api\OrderRepositoryInterface;
use Packs\Magento2\Helper\Adminhtml\Data as CustomHelper;
use Packs\Magento2\Model\Export\Productinfo;

class Grid extends Template
{
    protected $_shipmentModelFactory;
    protected $_resource;
    protected $_formKey;
    protected $_timezone;
    protected $_customHelper;
    protected $_orderRepository;
    protected $_productInfo;

    public function __construct(
        DateTime $date,
        FormKey $formKey,
        Context $context,
        ShipmentFactory $shipmentModelFactory,
        ResourceConnection $Resource,
        CustomHelper $helper,
        OrderRepositoryInterface $orderRepository,
        Productinfo $productInfo
    ) {
        $this->_date = $date;
        $this->_shipmentModelFactory = $shipmentModelFactory;
        $this->_resource = $Resource;
        $this->_formKey = $formKey;
        $this->_customHelper = $helper;
        $this->_orderRepository = $orderRepository;
        $this->_productInfo = $productInfo;
        parent::__construct($context);
    }

    protected function _prepareLayout()
    {
        $text = $this->getJoinData();
        $this->setText($text);
    }

    public function getDate()
    {
        $date = $this->_date->gmtDate();
        return $date;
    }

    public function getTomorrowDate()
    {
        $date = $this->_date->gmtDate(strtotime('tomorrow'));
        return $date;
    }

    public function getJoinData()
    {
        $collection = $this->_shipmentModelFactory->create()->getCollection();
        $sales_order = $this->_resource->getTableName('sales_order');

        $collection->getSelect()->joinRight(array('order' => $sales_order),
            'main_table.magento_order_id = order.increment_id')->where('main_table.confirm_status IS NULL');

        return $collection;

    }
    public function getOrderIds(){
        $ids = $this->_customHelper->getOrderIds();
        return $ids;
    }
    public function getOrderIncrementIds(){

        $ids = $this->getOrderIds();
        $orderIncrementIds = array();
        foreach($ids as $id){
            $order = $this->_orderRepository->get($id);
            $orderIncrementId = $order->getIncrementId();
            array_push($orderIncrementIds,$orderIncrementId);
        }
        return $orderIncrementIds;
    }

    public function getProductInfo(){
        return $this->_productInfo->getProductInfo();
    }

}
