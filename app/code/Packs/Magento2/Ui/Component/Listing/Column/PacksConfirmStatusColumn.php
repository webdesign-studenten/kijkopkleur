<?php

namespace Packs\Magento2\Ui\Component\Listing\Column;

 use Magento\Ui\Component\Listing\Columns\Column;

 use \Magento\Sales\Api\OrderRepositoryInterface;
 use \Magento\Framework\View\Element\UiComponent\ContextInterface;
 use \Magento\Framework\View\Element\UiComponentFactory;
 use \Magento\Framework\Api\SearchCriteriaBuilder;

class PacksConfirmStatusColumn extends Column
{
    protected $_orderRepository;
    protected $_searchCriteria;


    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        OrderRepositoryInterface $orderRepository,
        SearchCriteriaBuilder $criteria,
        array $components = [],
        array $data = []
    )
    {
        $this->_orderRepository = $orderRepository;
        $this->_searchCriteria = $criteria;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if($item['confirm_status']){
                    $status = 'Voorgemeld';
                    $item[$this->getData('name')] = '<span style="background:green; color: white; width:100%; padding-top: 0.5em; padding-bottom: 0.5em; float:left; text-align:center;">' . $status . '</span>';
                }else{
                    $status = 'Niet voorgemeld';
                    $item[$this->getData('name')] = '<span style="background:orangered; color: white; width:100%; padding-top: 0.5em; padding-bottom: 0.5em; float:left; text-align:center;">' . $status . '</span>';
                }
            }
        }
        return $dataSource;
    }

}

