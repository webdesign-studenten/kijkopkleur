<?php

/**
*
* Do not edit or add to this file if you wish to upgrade the module to newer
* versions in the future. If you wish to customize the module for your
* needs please contact us to https://www.milople.com/contact-us.html
*
* @category    Ecommerce
* @package     Milople_VATExempt
* @copyright   Copyright (c) 2017 Milople Technologies Pvt. Ltd. All Rights Reserved.
* @url         https://www.milople.com/magento-extensions/vat-exempt-m2.html
*
**/

namespace Milople\Vatexempt\Block\Adminhtml\Reports\Details;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Framework\Module\Manager
    */
    protected $moduleManager;

    /**
     * @var \Magento\Grid\Model\GridFactory
     */
    protected $_gridFactory;

    /**
     * @var \Magento\Grid\Model\Status
    */
    protected $_status;
	
	/**
     * @var \Milople\Vatexempt\Model\Details
    */
	protected $collection;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Sales\Model\Order\ItemFactory $productFactory,
     * @param \Magento\Framework\Registry $coreRegistry,
     * @param \Magento\Reports\Model\Grouped\CollectionFactory $collectionFactory,
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
	 * @param \Milople\Vatexempt\Model\Details $detailsFactory,
	 * @param \Milople\Vatexempt\Model\Conditions $conditionsModel
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
    */
	
    protected $productFactory;
    protected $_columnGroupBy = 'period';
    protected $_countTotals = true;
    protected $_productCollectionFactory;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Sales\Model\Order\ItemFactory $productFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Reports\Model\Grouped\CollectionFactory $collectionFactory,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
		\Milople\Vatexempt\Model\Details $detailsFactory,
		\Milople\Vatexempt\Model\Conditions $conditionsModel,
        \Milople\Vatexempt\Controller\Adminhtml\Reports\Details $details,
        array $data = []
    ) {
        $this->productFactory = $productFactory;
        $this->_categoryCollectionFactory = $categoryCollectionFactory;
        $this->_coreRegistry = $coreRegistry;
        $this->_collectionFactory = $collectionFactory;
		$this->_gridFactory  = $detailsFactory;
		$this->conditionsModel = $conditionsModel;
        $this->details = $details;
        parent::__construct($context, $backendHelper, $data);
    }
    
	/**
     * @return void
    */
    protected function _construct()
    {
        parent::_construct();
         $this->setUseAjax(false);
        $this->setCountTotals(true);
        $this->setFilterVisibility(false);
        $this->details->_initReportAction($this);
    }
	
    public function getCollection()
    {
        if ($this->_collection === null) {
            $this->setCollection($this->_collectionFactory->create());
        }
        return $this->_collection;
    }
	
    public function getResourceCollectionName()
    {
        return 'Milople\Vatexempt\Model\ResourceModel\Report\Order\Collection';
    }

    protected function _prepareCollection()
    {
		$filterData = $this->getFilterData();
		if ($filterData->getData('from') == null || $filterData->getData('to') == null) {
			$this->setCountTotals(true);
            $this->setCountSubTotals(false);
			return parent::_prepareCollection();
        }
		
		$collection = $this->_gridFactory->getCollection();
		$collection->getSelect()
			->joinLeft(
			['vatexempt_medicalcondition'=>$collection->getTable('vatexempt_medicalcondition')],
			'main_table.condition_id = vatexempt_medicalcondition.condition_id',
			[
			 'condition_name'=>'vatexempt_medicalcondition.condition_name'
			])->joinLeft(
			['sales_order_grid'=>$collection->getTable('sales_order_grid')],
			'main_table.order_id = sales_order_grid.increment_id',
			[
			 'grand_total'=>'sales_order_grid.grand_total',
			 'increment_id'=>'sales_order_grid.increment_id'
			]);
			
		$startDate = $filterData->getData('from');
		$endDate = $filterData->getData('to');
		$collection->setOrder('detail_id','DESC');
		$collection->getSelect()->assemble();
		$collection->getSelect()->__toString();
		$collection->getSelect()
            ->columns([
                'total' => new \Zend_Db_Expr('sum(grand_total)')
                ])
            ->group('increment_id');
		$collection->addFieldToFilter('main_table.created_at', array('from'=>$startDate, 'to'=>$endDate));
		$this->setCollection($collection);
		return $this;
    }
    public function getTotals()
    {
		$totals = new \Magento\Framework\DataObject();
        $fields = array(
            'grand_total' => 0

        );
        foreach ($this->getCollection() as $item) {
            foreach ($fields as $field => $value) {
				$fields[$field] += $item->getData($field);
            }
        }
        $totals->setData($fields);
        return $totals;
    }
    
	/**
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
    */
	
    protected function _prepareColumns()
    {
        $this->addColumn(
            'increment_id',
            [
                'header'     => __('Order ID'),
                'index'     => 'increment_id',
                'totals_label' => __('Total'),
                'sortable' => false,
                'filter'  => false
            ]
        );
        $this->addColumn(
            'product_name',
            [
                'header'     => __('Product Name'),
                'index'     => 'product_name',
                'sortable' => false,
                'filter'  => false
            ]
        );
		$this->addColumn(
            'condition_name',
            [
                'header'     => __('Condition Name'),
                'index'     => 'condition_name',
                'sortable' => false,
                'filter'  => false
            ]
        );
		$this->addColumn(
            'created_at',
            [
                'header'     => __('Created Date'),
                'index'     => 'created_at',
				'renderer' => \Magento\Reports\Block\Adminhtml\Sales\Grid\Column\Renderer\Date::class,
                'sortable' => false,
                'filter'  => false
            ]
        );
        $this->addColumn(
            'grand_total',
            [
                'header'     => __('Grand Total'),
                'index'     => 'grand_total',
                'type' => 'number',
				'total'     => 'grand_total',
                'sortable' => false,
                'filter'  => false
            ]
        );
    
        $block = $this->getLayout()->getBlock('grid.bottom.links');
        if ($block) {
            $this->setChild('grid.bottom.links', $block);
        }
        $this->addExportType('*/*/exportDetailsCsv', __('CSV'));
        $this->addExportType('*/*/exportDetailsExcel', __('Excel XML'));
        return parent::_prepareColumns();
    }
}
