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

namespace Milople\Vatexempt\Observer;

class Change implements \Magento\Framework\Event\ObserverInterface
{
	public function __construct(

        \Magento\Checkout\Model\Session $checkoutSession,
        \Psr\Log\LoggerInterface $logger,
		\Magento\Catalog\Model\Product $productModel,
		\Magento\Framework\App\RequestInterface $request,
		\Magento\Catalog\Model\ProductFactory $productFactory,
		\Milople\Vatexempt\Helper\Data $data_helper
    ) {
        $this->logger = $logger;
		$this->productFactory= $productFactory;
        $this->checkoutSession = $checkoutSession;
		$this->helper = $data_helper;
		$this->_request = $request;
		$this->products = $productModel;
    }

	  # Check everytime when price load and set the tax class to none

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
		\Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->log(100,print_r("***Observer is called***",true));
		$controller = $observer->getControllerAction();
		$moduleName = $this->_request->getModuleName();
		$controllerName = $this->_request->getControllerName();
		
		\Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->log(100,print_r($moduleName,true));
		\Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->log(100,print_r($controllerName,true));
		\Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->log(100,print_r($this->checkoutSession->getVatStatus(),true));
		if($moduleName=='checkout' && $controllerName=='cart'){
			$this->checkoutSession->setVatStatus(0);
		}
		$items = $observer->getEvent()->getQuote()->getAllItems();
		$applyTo=$this->helper->getConfig('vatexempt/generalSettings/vatexempt_apply_to');
        \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->log(100,print_r($applyTo,true));
		foreach($items as $item)
        {
			\Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->log(100,print_r("Inside foreach",true));
			\Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->log(100,print_r($this->checkoutSession->getVatStatus(),true));
			$product=$this->productFactory->create();
			$product=$product->load($item->getProductId());
        	if($this->checkoutSession->getVatStatus()=="1"){
				\Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->log(100,print_r("Inside main if",true));
				\Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->log(100,print_r($applyTo,true));
				if($product->getVatstatus()==1 || $applyTo=='1'){
					\Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->log(100,print_r("Apply vatexempt",true));
					$item->getProduct()->setTaxClassId(0);
					$item->save();
				}else{
					\Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->log(100,print_r("else condition",true));
					$item->getProduct()->setTaxClassId(2);
					$item->save();
				}
			}else{
				\Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->log(100,print_r("main  else condition",true));
				$item->getProduct()->setTaxClassId(2);
			}
		}		
    }
}