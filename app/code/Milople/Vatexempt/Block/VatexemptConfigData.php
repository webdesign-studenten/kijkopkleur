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

namespace Milople\Vatexempt\Block;

class VatexemptConfigData extends \Magento\Framework\View\Element\Template

{

	public $configData;

	public $scopeConfig;

	public $checkoutSession;

	public $productModel;

	public $medicalConditions;

	public function __construct(

    \Magento\Framework\View\Element\Template\Context $context,

		\Magento\Checkout\Model\CompositeConfigProvider $configProvider,

		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,

		\Magento\Catalog\Model\Product $productModel,

		\Magento\Catalog\Model\ProductFactory $productFactory,

		\Milople\Vatexempt\Model\Conditions $vatexemptConditions,

		\Magento\Checkout\Model\Session $checkoutSession,

		\Psr\Log\LoggerInterface $logger,

        array $data = []

    ) {

    $this->configData = $configProvider;

		$this->scopeConfig = $scopeConfig;

		$this->checkoutSession = $checkoutSession;

		$this->productModel = $productModel;

		$this->productFactory= $productFactory;

		$this->medicalConditions = $vatexemptConditions;

    $this->logger=$logger;

		parent::__construct($context, $data);

	}

	

	protected function _prepareLayout()

	{

		return parent::_prepareLayout();

	}

	# Set the VAT configuration in tempalte

  public function getVatexemptConfig()

	{

			$products = array();

			$productOption = $this->scopeConfig->getValue('vatexempt/generalSettings/vatexempt_apply_to', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

			$quote = $this->checkoutSession->getQuote();

			foreach ($quote->getAllVisibleItems() as $item) {

							$product=$this->productFactory->create();

							$product=$product->load($item->getProductId());

							$vatvalue= $product->getData('vatstatus'); 

							if ($product->getVatstatus()=='1' || $productOption=='1'){

									$products[] = [

										'id'	=>	$item->getProductId(),

										'name'	=>	$this->productModel->load($item->getProductId())->getName(),

										'status'=>	$product->getVatstatus(),

								];

							}

					}

			$vatexemptData = array();

			$vatexemptData['vatexemptStatus'] = $this->scopeConfig->getValue('vatexempt/licenseOptions/vatexempt_status', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

			$vatexemptData['vatexemptApplyTo'] = $this->scopeConfig->getValue('vatexempt/generalSettings/vatexempt_apply_to', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

			$vatexemptData['vatexemptShowLink'] = $this->scopeConfig->getValue('vatexempt/generalSettings/vatexempt_show_link', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

			$vatexemptData['vatexemptLinkText'] = $this->scopeConfig->getValue('vatexempt/generalSettings/vatexempt_link_text', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

			$vatexemptData['vatexemptTermsandconditions'] = $this->scopeConfig->getValue('vatexempt/generalSettings/vatexempt_termsandconditions', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

			$vatexemptData['vatexemptFile'] = $this->scopeConfig->getValue('vatexempt/generalSettings/vatexempt_upload_file', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

			$vatexemptData['vatexemptProductList'] = $products;

			$vatexemptData['vatexemptConditions'] = $this->medicalConditions->getCollection()->getData();

			$vatexemptData['vatexemptURL'] = $this->getUrl()."rest/default/V1/vatexempt/setdata";



			return $vatexemptData;

	}

}

