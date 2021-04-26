<?php

namespace Packs\Magento2\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;

use \Magento\Sales\Api\OrderRepositoryInterface;
use \Magento\Framework\View\Element\UiComponent\ContextInterface;
use \Magento\Framework\View\Element\UiComponentFactory;
use \Magento\Framework\Api\SearchCriteriaBuilder;
use \Packs\Magento2\Model\ShipmentFactory;

class PacksConfirmDateColumn extends Column
{

    protected $_orderRepository;
    protected $_searchCriteria;
    protected $_packsShipmentFactory;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        OrderRepositoryInterface $orderRepository,
        SearchCriteriaBuilder $criteria,
        array $components = [],
        array $data = [],
        ShipmentFactory $packsShipmentFactory
    )
    {
        $this->_orderRepository = $orderRepository;
        $this->_searchCriteria = $criteria;
        $this->_packsShipmentFactory = $packsShipmentFactory;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }



    public function prepareDataSource(array $dataSource)
    {
        $resultFactory = $this->_packsShipmentFactory->create();
        $shipmentCollection = $resultFactory->getCollection();
        $magentoShippingData = array();
        $packsShipmentData = $shipmentCollection->getData();

        foreach($packsShipmentData as $data){
            $magentoShippingData[$data['magento_order_id']] = $data['created_at'];
        }

        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if(array_key_exists($item['entity_id'],$magentoShippingData)){
                    $item[$this->getData('name')] = '<span style="color:green">'.$magentoShippingData[$item['entity_id']].'</span>';
                }else{
                    $item[$this->getData('name')] = '<span style="color:red">Bestaat niet</span>';
                }

            }
        }
        return $dataSource;
    }

}

