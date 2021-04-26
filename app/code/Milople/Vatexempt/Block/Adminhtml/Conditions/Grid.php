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
namespace Milople\Vatexempt\Block\Adminhtml\Conditions;
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;
 
    /**
     * @var \Milople\Vatexempt\Model\GridFactory
     */
    protected $_gridFactory;
 
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Milople\Vatexempt\Model\GridFactory $gridFactory
     * @param \Milople\Vatexempt\Model\Status $status
     * @param \Milople\Framework\Module\Manager $moduleManager
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Milople\Vatexempt\Model\ConditionsFactory $conditionsFactory,       
        \Magento\Framework\Module\Manager $moduleManager,
        array $data = []
    ) {
        $this->_gridFactory = $conditionsFactory;        
        $this->moduleManager = $moduleManager;
        parent::__construct($context, $backendHelper, $data);
    }
 
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('condition_id');
        $this->setDefaultSort('condition_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        //$this->setUseAjax(true);
        $this->setVarNameFilter('grid_record');
    }
 
    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->_gridFactory->create()->getCollection();
        $this->setCollection($collection);
 
        parent::_prepareCollection();
        return $this;
    }
 
    /**
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'condition_id',
            [
                'header' => __('ID'),
                'type' => 'number',
                'index' => 'condition_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        $this->addColumn(
            'condition_name',
            [
                'header' => __('Title'),
                'index' => 'condition_name',
                'class' => 'xxx'
            ]
        );
 
        $this->addColumn(
            'published_at',
            [
                'header' => __('Publish Date'),
                'index' => 'published_at'
            ]
        );
		 
 
 
        $this->addColumn(
            'status',
            [
                'header' => __('Status'),
                'index' => 'status',
                'type' => 'options',
                'options' => [1 => __('Enabled'), 0 => __('Disabled')]
            ]
        );
 
 
        $this->addColumn(
            'edit',
            [
                'header' => __('Edit'),
                'type' => 'action',
                'getter' => 'getId',
                'actions' => [
                    [
                        'caption' => __('Edit'),
                        'url' => [
                            'base' => '*/*/edit'
                        ],
                        'field' => 'condition_id'
                    ]
                ],
                'filter' => false,
                'sortable' => false,
                'index' => 'stores',
                'header_css_class' => 'col-action',
                'column_css_class' => 'col-action'
            ]
        );
 
 
        $block = $this->getLayout()->getBlock('grid.bottom.links');
        if ($block) {
            $this->setChild('grid.bottom.links', $block);
        }
 
        return parent::_prepareColumns();
    }
 
    /**
     * @return $this
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('condition_id');
        $this->getMassactionBlock()->setFormFieldName('condition_id');
 
        $this->getMassactionBlock()->addItem(
            'delete',
            [
                'label' => __('Delete'),
                'url' => $this->getUrl('vatexempt/*/massDelete'),
                'confirm' => __('Are you sure?')
            ]
        );
 
        $statuses = [0 => __('Disabled'),1 => __('Enabled')];
 
        //array_unshift($statuses, ['label' => '', 'value' => '']);
        $this->getMassactionBlock()->addItem(
            'status',
            [
                'label' => __('Change Status'),
                'url' => $this->getUrl('vatexempt/*/massStatus', ['_current' => true]),
                'additional' => [
                    'visibility' => [
                        'name' => 'status',
                        'type' => 'select',
                        'class' => 'required-entry',
                        'label' => __('Status'),
                        'values' => $statuses
                    ]
                ]
            ]
        );
 
 
        return $this;
    }
 
    /**
     * @return string
     */
    //public function getGridUrl()
    //{
    //    return $this->getUrl('conditions/*/grid', ['_current' => true]);
    //}
 
    
    public function getRowUrl($row)
    {
        return $this->getUrl(
            'vatexempt/*/edit',
            ['condition_id' => $row->getId()]
        );
    }
}