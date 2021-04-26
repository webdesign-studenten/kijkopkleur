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

namespace Milople\Vatexempt\Controller\Adminhtml\Conditions;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Milople\Vatexempt\Model\ResourceModel\Conditions\CollectionFactory;

class MassStatus extends \Magento\Backend\App\Action
{
	/**
     * Massactions filter.​_
     * @var Filter
    */
    protected $_filter;

    /**
     * @var CollectionFactory
    */
    protected $_collectionFactory;
	
	public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory
    ) {

        $this->_filter = $filter;
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($context);
    }
   /**
    * @return void
   */
	public function execute()
	{
		$status = (int) $this->getRequest()->getParam('status');
		$collection = $this->_filter->getCollection($this->_collectionFactory->create());
		$collectionSize = $collection->getSize();
        $recordDeleted = 0;
		foreach ($collection->getItems() as $record) {
			$model = $this->_objectManager->create(\Milople\Vatexempt\Model\Conditions::class);
			$model->load($record->getConditionId());
			$model->setStatus($status);
			$model->save();
        }
        $this->messageManager->addSuccess(__('A total of %1 record(s) have been updated.', $collectionSize));
        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('*/*/index');
	}
}