<?php
/**
*
* Do not edit or add to this file if you wish to upgrade the module to newer
* versions in the future. If you wish to customize the module for your
* needs please contact us to https://www.milople.com/contact-us.html
*
* @category    Ecommerce
* @package     Milople_Partialpaymentauto
* @copyright   Copyright (c) 2019 Milople Technologies Pvt. Ltd. All Rights Reserved.
* @url         https://www.milople.com/magento2-extensions/partial-payment-m2.html
*
**/
namespace Milople\Vatexempt\Block\Adminhtml\Reports;

class Details extends \Magento\Backend\Block\Widget\Grid\Container
{
	/**
     * @var string
     */
    protected $_template = 'report/details.phtml';
 
    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param array $data
     */
    // public function __construct(
    //     \Magento\Backend\Block\Widget\Context $context,
    //     array $data = []
    // ) {
    //     parent::__construct($context, $data);
    // }

    protected function _construct()
    {
        $this->_blockGroup = 'Milople_Vatexempt';
        $this->_controller = 'adminhtml_reports_details';

        parent::_construct();
        $this->_headerText = __('Vatexempt Details Report');
        $this->buttonList->remove('add');
        $this->addButton(
            'filter_form_submit',
            ['label' => __('Show Report'), 'onclick' => 'filterFormSubmit()', 'class' => 'primary']
        );
    }
 
    /**
     * Prepare button and gridCreate Grid , edit/add grid row and installer in Magento2
     *
     * @return \Magento\Catalog\Block\Adminhtml\Product
     */
    protected function _prepareLayout()
    {
        $this->setChild(
            'grid',
           $this->getLayout()->createBlock('Milople\Vatexempt\Block\Adminhtml\Reports\Details\Grid', 'grid.view.grid')
        );
        return parent::_prepareLayout();
    }
 
    /**
     * Render grid
     *
     * @return string
     */
    public function getGridHtml()
    {
        return $this->getChildHtml('grid');
    }
}
