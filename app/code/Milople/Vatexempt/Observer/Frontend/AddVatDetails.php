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

namespace Milople\Vatexempt\Observer\Frontend;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
class AddVatDetails implements ObserverInterface
{
	public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
		\Milople\Vatexempt\Model\Details $detailsModel,
		\Magento\Catalog\Model\Product $productModel,
		\Milople\Vatexempt\Helper\Data $data_helper,
		\Magento\Catalog\Model\ProductFactory $productFactory,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->logger = $logger;
		$this->products = $productModel;
		$this->productFactory= $productFactory;
        $this->checkoutSession = $checkoutSession;
		$this->detailsModel=$detailsModel;
		$this->helper = $data_helper;
    }
    /**
     * Add VAT validation request date and identifier to order comments
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
		\Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->log(100,print_r("***AddVatDetails Observer is called***",true));
		$order = $observer->getEvent()->getOrder();
		$order_id = $order->getIncrementId();
		$selectedVatStatus=$this->checkoutSession->getVatStatus();
		if($selectedVatStatus=='1'){
			$items = $order->getAllVisibleItems();
			$date=date('Y-m-d');
			$applyTo=$this->helper->getConfig('vatexempt/generalSettings/vatexempt_apply_to');
			$conditionId=$this->checkoutSession->getVatSelectedReason();
			$conditionName=$this->helper->getConditionName($conditionId);
			foreach($items as $item)
			{
				\Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->log(100,print_r("Save data here",true));
				\Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->log(100,print_r($this->checkoutSession->getFile(),true));
				$product=$this->productFactory->create();
				$product=$product->load($item->getProductId());
				if($product->getVatstatus()=='1'|| $applyTo=='1'){                     
					$model = $this->detailsModel
						->setProductId($product->getId())
					->setOrderId($order_id)
					->setConditionId($this->checkoutSession->getVatSelectedReason())
					->setSelectedFile($this->checkoutSession->getSelectedFile())
					->setProductName($product->getName())
					->setProductSku($product->getSku())
					->setApplicantName($this->checkoutSession->getVatApplientName())
					->setCreatedAt($date)
					->setpublishedAt($date)
					->setFile($this->checkoutSession->getFile())
					->save();
					$model->unsetData();
				}
			}
		}
		$this->checkoutSession->unsVatStatus();
    }
}

