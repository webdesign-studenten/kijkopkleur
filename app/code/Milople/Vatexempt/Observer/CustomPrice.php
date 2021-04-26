<?php
    
namespace Milople\Vatexempt\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;

class CustomPrice implements ObserverInterface
{
    public function execute(\Magento\Framework\Event\Observer $observer) {
        
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $checkoutsession = $objectManager->get('Magento\Checkout\Model\Session');
        if($objectManager->get('Magento\Checkout\Model\Session')->getVatStatus() == "1"){
            $heper  = $objectManager->get('Magento\Catalog\Helper\Data');
            $productFactory = $objectManager->get('Magento\Catalog\Model\ProductFactory');
            $taxCalculation=$objectManager->create( '\Magento\Tax\Api\TaxCalculationInterface');
            $storeManager= $objectManager->create('\Magento\Store\Model\StoreManagerInterface');
            $customerSession = $objectManager->create('Magento\Customer\Model\Session');
            $currencysymbol = $objectManager->get('Magento\Store\Model\StoreManagerInterface');
            $currencyCode = $currencysymbol->getStore()->getCurrentCurrencyCode();
            $currency =  $objectManager->create('Magento\Directory\Model\CurrencyFactory')->create()->load($currencyCode); 
            
            $customerId=$customerSession->getCustomer()->getId();
            $storeId= $storeManager->getStore()->getId();
            
            $item = $observer->getEvent()->getData('quote_item');
            $product = $item->getProduct();
            $productTaxClassId = $product->getData('tax_class_id');
            $rate = $taxCalculation->getCalculatedRate($productTaxClassId, $customerId, $storeId);
            $priceExcludingTax = $product->getFinalPrice()- ($product->getPrice() * ($rate / 100));
            
            $product=$productFactory->create();
            $product=$product->load($item->getProductId());
            $item->getProduct()->setTaxClassId(0);
            

            //$item = $observer->getEvent()->getData('quote_item');         
            $item = ( $item->getParentItem() ? $item->getParentItem() : $item );
            $price = $priceExcludingTax;
            $item->setCustomPrice($price);
            $item->setOriginalCustomPrice($price);
            $item->getProduct()->setIsSuperMode(true);
        }
    }
}