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
class Save extends \Magento\Backend\App\Action
{	private $dateFilter;	public function __construct(		\Magento\Backend\App\Action\Context $context,		\Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter	)	{		parent::__construct($context);		$this->dateFilter = $dateFilter;	}
	 /**
	 * Save the Medical Condition Data
	 * @return void
	 */
   public function execute()
   {
      $isPost = $this->getRequest()->getPost();
 
      if ($isPost) {
         $condition = $this->_objectManager->create('Milople\Vatexempt\Model\Conditions');
         $condition_id = $this->getRequest()->getParam('condition_id');
 
         if ($condition_id) {
            $condition->load($condition_id);
         }
         $formData = $this->getRequest()->getPostValue();			$formData['published_at'] = $this->dateFilter->filter($formData['published_at']);			
         $condition->setData($formData);
         
         try {
            // Save condition
            $condition->save();
 
            // Display success message
            $this->messageManager->addSuccess(__('The condition has been saved.'));
 
            // Check if 'Save and Continue'
            if ($this->getRequest()->getParam('back')) {
               $this->_redirect('*/*/edit', ['condition_id' => $condition->getId(), '_current' => true]);
               return;
            }
             // Go to grid page
            $this->_redirect('*/*/');
            return;
         } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
         }
 
         $this->_getSession()->setFormData($formData);
         $this->_redirect('*/*/edit', ['condition_id' => $condition_id]);
      }
   }
}
