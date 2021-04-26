<?php

namespace Packs\Magento2\Controller\Adminhtml\Shipment;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\Controller\ResultFactory;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Packs\Magento2\Helper\Adminhtml\Data as CustomHelper;

class Grid extends Action
{

    /**
     * @var \Magento\Ui\Component\MassAction\Filter
     */
    protected $_filter;
    protected $_selectedCollection;
    protected $_collectionFactory;
    protected $_customHelper;
    protected $resultPageFactory = false;

    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        CustomHelper $helper,
        PageFactory $pageFactory
    ) {
        $this->_filter = $filter;
        $this->_collectionFactory = $collectionFactory;
        $this->_customHelper = $helper;
        $this->resultPageFactory = $pageFactory;
        parent::__construct($context);
    }

    public function execute()
    {

        $collection = $this->_filter->getCollection($this->_collectionFactory->create());
        try {
            $entityDeleted = 0;
            $ids = array();
            foreach ($collection->getAllIds() as $id) {
                array_push($ids, $id);
            }
            $this->_customHelper->setOrderIds($ids);

            $this->messageManager->addSuccess(__('A total of %1 record(s) were selected.', count($ids)));
        } catch (\Exception $e) {
            $this->messageManager->addError(__($e->getMessage()));
        }
        $resultPage = $this->resultPageFactory->create();
        //Set the menu which will be active for this page
        $resultPage->setActiveMenu('Magento_Sales::sales_order');

        //Set the header title of grid
        $resultPage->getConfig()->getTitle()->prepend(__('Voormelden Packs'));
        //Add bread crumb
        $resultPage->addBreadcrumb(__('Orders'), __('Orders'));
        $resultPage->addBreadcrumb(__('Packs voormelden'), __('Packs voormelden'));
        return $resultPage;
    }
}