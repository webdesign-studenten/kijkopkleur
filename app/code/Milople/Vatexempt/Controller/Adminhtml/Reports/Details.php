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
namespace Milople\Vatexempt\Controller\Adminhtml\Reports;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Backend\App\Action;
use Magento\Reports\Controller\Adminhtml\Report\AbstractReport;

class Details extends AbstractReport
{
	/**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;
	/**
     * @var PageFactory
     */
    protected $resultPageFactory;
  
    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter,
        TimezoneInterface $timezone,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context,$fileFactory,$dateFilter,$timezone);
        $this->resultPageFactory = $resultPageFactory;
    }
  /**
     * Index action
     *
     * @return void
     */
    public function execute()
    {
        $this->_initAction()->_setActiveMenu(
            'Milople_Vatexempt::vatexemptreports_details'
        );
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Vatexempt Details Reports'));
		$filterFormBlock = $this->_view->getLayout()->getBlock('grid.filter.form');		
		$gridBlock = $this->_view->getLayout()->getBlock('vatexempt_reports_details');
		$this->_initReportAction([$gridBlock, $filterFormBlock]);
		$this->_view->renderLayout();
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        // $resultPage = $this->resultPageFactory->create();
        // $resultPage->setActiveMenu('Milople_Personalizedcool::personalizedcoolreports_products');
        // $resultPage->getConfig()->getTitle()->prepend(__('Personalizedcool Regular Discount Reports'));			
        // return $resultPage;
    }
}
