<?php
/**
*
* Do not edit or add to this file if you wish to upgrade the module to newer
* versions in the future. If you wish to customize the module for your
* needs please contact us to https://www.milople.com/contact-us.html
*
* @category    Ecommerce
* @package     Milople_Personlized
* @copyright   Copyright (c) 2016 Milople Technologies Pvt. Ltd. All Rights Reserved.
* @url         https://www.milople.com/magento2-extensions/personalized-products-m2.html
*
**/
namespace Milople\Vatexempt\Block\Product;

use Magento\Catalog\Api\CategoryRepositoryInterface;

class ListProduct extends \Magento\Catalog\Block\Product\ListProduct
{
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        CategoryRepositoryInterface $categoryRepository,
        \Magento\Framework\Url\Helper\Data $urlHelper,
         \Magento\Directory\Model\CountryFactory $countryFactory,
        \Milople\Vatexempt\Helper\Data $vatexempt_helper,
        \Magento\Tax\Api\TaxCalculationInterface $taxCalculation,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\SessionFactory $customerSession,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\Framework\Module\Manager $moduleManager,
          array $data = []
    ) {
        $this->vatexempt_helper=$vatexempt_helper;
        $this->_taxCalculation=$taxCalculation;
        $this->_countryFactory = $countryFactory;
        $this->_storeManager = $storeManager;
        $this->_customerSession = $customerSession->create();
        $this->_currencyFactory = $currencyFactory;
        $this->_moduleManager = $moduleManager;
        parent::__construct($context, $postDataHelper, $layerResolver, $categoryRepository, $urlHelper, $data);
    }
    /*
    * Show Personalized It! Button on list page.
    */
    public function getProductDetailsHtml(\Magento\Catalog\Model\Product $product)
    {
        $status=$this->vatexempt_helper->getConfig('vatexempt/licenseOptions/vatexempt_status');
        $html='';
        $renderer = $this->getDetailsRenderer($product->getTypeId());
        if ($renderer) {
            $renderer->setProduct($product);
            $isVatEanble = $this->_moduleManager->isEnabled('Milople_Vatexempt');
            $enableFromProduct=$product->getVatstatus();
            $productTaxClassId = $product->getTaxClassId();
            // $productRateId = $taxAttribute->getValue();
            $countryCode = $this->vatexempt_helper->getConfig(\Magento\Shipping\Model\Config::XML_PATH_ORIGIN_COUNTRY_ID);
            $customerTaxClassId = $this->vatexempt_helper->getConfig('tax/classes/default_customer_tax_class');
            $productTaxClassId = $product->getData('tax_class_id');
            $currencyCode = $this->_storeManager->getStore()->getCurrentCurrencyCode();
            $currencySymbol = $this->_storeManager->getStore()->getCurrencySymbol();
            $storeId= $this->_storeManager->getStore()->getId();
            $customerId=$this->_customerSession->getId();
            $currency = $this->_currencyFactory->create()->load($currencyCode);
            $currencySymbol = $currency->getCurrencySymbol(); 
            $rate = $this->_taxCalculation->getCalculatedRate($productTaxClassId, $customerId, $storeId);
            $priceExcludingTax = $product->getFinalPrice();
            $priceIncludingTax = $priceExcludingTax + ($priceExcludingTax * ($rate / 100));
            // $storeId = $storeManager->getStore()->getId();
                // if(($status && $enableFromProduct) || ($product->getTypeId()=='personalized' && $status)) {            
                // $buttonLabel=$this->vatexempt_helper->getConfig('personalizedrich/general_setting_group/button_label');
            if ($isVatEanble) {
                if ($status == 1) {
                    if ($enableFromProduct) {
                        $html = '<span
                            data-price-type="basePrice"
                            class="price-wrapper price-excluding-tax">
                            <span class="price"> Incl.Tax: '.$currencySymbol.round($priceIncludingTax, 2).'</span></span>'; 
                    }
                }
            }

        }
           return $renderer->toHtml() . $html;
        
        
    }
}
